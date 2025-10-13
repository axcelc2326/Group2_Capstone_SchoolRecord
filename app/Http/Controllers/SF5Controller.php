<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\SF5Record;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SF5Controller extends Controller
{
    public function showForm()
    {
        $teacherId = Auth::id();

        // Fetch teacher's classes
        $classes = ClassModel::where('teacher_id', $teacherId)
            ->select('id', 'name', 'grade_level')
            ->get();

        // Fetch existing SF5 records for this teacher
        $sf5Records = SF5Record::with('class')
            ->where('teacher_id', $teacherId)
            ->latest()
            ->get(['id', 'region', 'division', 'school_id', 'school_name', 'school_year', 'class_id', 'created_at']);

        return Inertia::render('Teacher/SF5Form', [
            'classes' => $classes,
            'sf5Records' => $sf5Records
        ]);
    }

    public function download(Request $request)
    {
        $request->validate([
            'region' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'school_id' => 'required|string|max:50',
            'school_name' => 'required|string|max:255',
            'school_year' => 'required|string|max:50',
            'school_head_chair' => 'required|string|max:255',
            'class_id' => 'required|integer',
        ]);

        $teacherId = Auth::id();

        $class = ClassModel::with(['students.grades.subject', 'teacher'])
            ->where('teacher_id', $teacherId)
            ->where('id', $request->class_id)
            ->firstOrFail();

        // initialize totals
        $totals = [
            'male' => 0, 'female' => 0, 'overall' => 0,
            'promoted_male' => 0, 'promoted_female' => 0,
            'conditional_male' => 0, 'conditional_female' => 0,
            'retained_male' => 0, 'retained_female' => 0,
            'below_75_male' => 0, 'below_75_female' => 0,
            'fair_75_79_male' => 0, 'fair_75_79_female' => 0,
            'satisfactory_80_84_male' => 0, 'satisfactory_80_84_female' => 0,
            'very_satisfactory_85_89_male' => 0, 'very_satisfactory_85_89_female' => 0,
            'outstanding_90_100_male' => 0, 'outstanding_90_100_female' => 0,
            'total_general_average' => 0,
        ];

        foreach ($class->students as $student) {
            $gender = strtolower($student->gender);
            $finals = $student->grades->groupBy('subject_id')->map(fn ($grades) => $grades->avg('grade'));
            $avg = $finals->avg();

            if (!$avg) continue;

            // gender-based increment
            $totals[$gender === 'male' ? 'male' : 'female']++;
            $totals['overall']++;
            $totals['total_general_average'] += $avg;

            // Action Taken
            if ($avg >= 75) {
                $totals["promoted_{$gender}"]++;
            } elseif ($avg >= 70) {
                $totals["conditional_{$gender}"]++;
            } else {
                $totals["retained_{$gender}"]++;
            }

            // Achievement Level
            if ($avg < 75) $totals["below_75_{$gender}"]++;
            elseif ($avg < 80) $totals["fair_75_79_{$gender}"]++;
            elseif ($avg < 85) $totals["satisfactory_80_84_{$gender}"]++;
            elseif ($avg < 90) $totals["very_satisfactory_85_89_{$gender}"]++;
            else $totals["outstanding_90_100_{$gender}"]++;
        }

        $totals['class_average'] = $totals['overall'] > 0
            ? number_format($totals['total_general_average'] / $totals['overall'], 2)
            : 0;

        // save record
        SF5Record::create(array_merge($request->only([
            'region', 'division', 'school_id', 'school_name',
            'school_year', 'school_head_chair', 'class_id'
        ]), [
            'teacher_id' => $teacherId,
            'male_count' => $totals['male'],
            'female_count' => $totals['female'],
            'overall_count' => $totals['overall'],
            'class_average' => $totals['class_average'],
        ] + $totals));

        // generate PDF
        $pdf = Pdf::loadView('pdf.sf5', [
            'class' => $class,
            'students' => $class->students,
            'teacher' => $class->teacher,
            'meta' => $request->only([
                'region', 'division', 'school_id', 'school_name', 'school_year', 'school_head_chair'
            ]),
            'totals' => $totals,
        ])->setPaper('legal', 'portrait');

        return $pdf->download('SF5_Report.pdf');
    }

    // ✏️ Edit an existing SF5 record (render edit form)
    public function edit($id)
    {
        $record = SF5Record::with('class')->findOrFail($id);

        return Inertia::render('Teacher/EditSF5', [
            'record' => $record,
        ]);
    }

    // 💾 Update existing SF5 record
    public function update(Request $request, $id)
    {
        $request->validate([
            'region' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'school_id' => 'required|string|max:50',
            'school_name' => 'required|string|max:255',
            'school_year' => 'required|string|max:50',
            'school_head_chair' => 'required|string|max:255',
        ]);

        $record = SF5Record::findOrFail($id);
        $record->update($request->only([
            'region', 'division', 'school_id', 'school_name',
            'school_year', 'school_head_chair'
        ]));

        return redirect()->route('sf5.form')->with('success', 'SF5 record updated successfully!');
    }

    // 🗑️ Delete a record
    public function destroy($id)
    {
        $record = SF5Record::findOrFail($id);
        $record->delete();

        return redirect()->route('sf5.form')->with('success', 'SF5 record deleted.');
    }

    // 📥 Download Again from record data
    public function downloadAgain($id)
    {
        $record = SF5Record::with('class.students.grades.subject', 'class.teacher')
            ->findOrFail($id);

        $class = $record->class;

        $pdf = Pdf::loadView('pdf.sf5', [
            'class' => $class,
            'students' => $class->students,
            'teacher' => $class->teacher,
            'meta' => $record->only([
                'region', 'division', 'school_id', 'school_name',
                'school_year', 'school_head_chair',
            ]),
            'totals' => $record->only([
                'male_count', 'female_count', 'overall_count', 'class_average',
                'promoted_male', 'promoted_female', 'conditional_male', 'conditional_female',
                'retained_male', 'retained_female',
                'below_75_male', 'below_75_female',
                'fair_75_79_male', 'fair_75_79_female',
                'satisfactory_80_84_male', 'satisfactory_80_84_female',
                'very_satisfactory_85_89_male', 'very_satisfactory_85_89_female',
                'outstanding_90_100_male', 'outstanding_90_100_female',
            ]),
        ])->setPaper('legal', 'portrait');

        return $pdf->download('SF5_Report_Reprint.pdf');
    }
}
