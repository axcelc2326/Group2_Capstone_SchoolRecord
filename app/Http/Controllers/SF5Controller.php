<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ClassModel; // assuming your class model is named ClassModel
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SF5Controller extends Controller
{
    // Show a form to input metadata before downloading PDF
    public function showForm()
    {
        $teacherId = Auth::id();

        $classes = ClassModel::where('teacher_id', $teacherId)
            ->select('id', 'name', 'grade_level')
            ->get();

        return Inertia::render('Teacher/SF5Form', [
            'classes' => $classes
        ]);
    }

    // Generate and download PDF
    public function download(Request $request)
    {
        $request->validate([
            'region'            => 'required|string|max:255',
            'division'          => 'required|string|max:255',
            'school_id'         => 'required|string|max:50',
            'school_name'       => 'required|string|max:255',
            'school_year'       => 'required|string|max:50',
            'school_head_chair' => 'required|string|max:255', // merged field
            'class_id'          => 'required|integer',
        ]);

        $teacherId = Auth::id();

        $class = ClassModel::with(['students.grades.subject', 'teacher'])
            ->where('teacher_id', $teacherId)
            ->where('id', $request->class_id)
            ->firstOrFail();

        $subjects = $class->students
            ->flatMap(fn ($student) => $student->grades->pluck('subject'))
            ->unique('id')
            ->values();

        // ---- Compute totals ----
        $totals = [
            'male'   => 0,
            'female' => 0,
            'overall'=> 0,
            'promoted' => 0,
            'with_honors' => 0,
            'with_high_honors' => 0,
            'with_highest_honors' => 0,
            'retained' => 0,
            'total_general_average' => 0,
        ];

        foreach ($class->students as $student) {
            // Count gender
            if (strtolower($student->gender) === 'male') {
                $totals['male']++;
            } else {
                $totals['female']++;
            }

            $totals['overall']++;

            // Compute general average
            $finals = $student->grades->groupBy('subject_id')->map(fn ($grades) => $grades->avg('grade'));
            $generalAverage = $finals->avg();

            if ($generalAverage) {
                $totals['total_general_average'] += $generalAverage;

                if ($generalAverage >= 90 && $generalAverage <= 94) {
                    $totals['with_honors']++;
                } elseif ($generalAverage >= 95 && $generalAverage <= 97) {
                    $totals['with_high_honors']++;
                } elseif ($generalAverage >= 98) {
                    $totals['with_highest_honors']++;
                } elseif ($generalAverage >= 75) {
                    $totals['promoted']++;
                } else {
                    $totals['retained']++;
                }
            } else {
                $totals['retained']++;
            }
        }

        // Compute class average grade (overall)
        $totals['class_average'] = $totals['overall'] > 0 
            ? number_format($totals['total_general_average'] / $totals['overall'], 2) 
            : 0;

        // ---- Pass everything to the PDF view ----
        $pdf = Pdf::loadView('pdf.sf5', [
            'class'    => $class,
            'students' => $class->students,
            'teacher'  => $class->teacher,
            'subjects' => $subjects,
            'meta'     => $request->only([
                'region',
                'division',
                'school_id',
                'school_name',
                'school_year',
                'school_head_chair',
            ]),
            'totals'   => $totals, // ✅ pass totals into the view
        ])->setPaper('legal', 'portrait');

        return $pdf->download('SF5_Report.pdf');
    }
}
