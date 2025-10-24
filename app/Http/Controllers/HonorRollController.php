<?php

namespace App\Http\Controllers;

use App\Models\HonorRoll;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class HonorRollController extends Controller
{
    /** 📄 Show Honor Roll main page */
    public function index()
    {
        $teacherId = Auth::id();

        $records = HonorRoll::with('class')
            ->where('teacher_id', $teacherId)
            ->orderByDesc('created_at')
            ->get();

        $classes = ClassModel::where('teacher_id', $teacherId)
            ->select('id', 'name', 'grade_level')
            ->get();

        return inertia('Teacher/HonorRollIndex', [
            'classes' => $classes,
            'records' => $records,
        ]);
    }

    /** 🧮 Generate and save Honor Roll PDF */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'school_year' => 'required|string|max:20',
            'quarter' => 'required|string|max:20',
            'principal_name' => 'required|string|max:255',
        ]);

        $teacher = Auth::user();

        // 🧭 Convert readable quarter into DB key
        $quarterMap = [
            '1st Quarter' => 'Q1',
            '2nd Quarter' => 'Q2',
            '3rd Quarter' => 'Q3',
            '4th Quarter' => 'Q4',
        ];
        $quarterKey = $quarterMap[$validated['quarter']] ?? null;

        if (!$quarterKey) {
            return back()->with('error', 'Invalid quarter selected.');
        }

        // 📊 Load students with their grades for the selected quarter
        $class = ClassModel::with(['students.grades' => function ($query) use ($quarterKey) {
            $query->where('quarter', $quarterKey);
        }])->findOrFail($validated['class_id']);

        // 🧮 Counters
        $withHonors = $withHighHonors = $withHighestHonors = 0;
        $honorStudents = [];

        foreach ($class->students as $student) {
            $grades = $student->grades;

            if ($grades->isEmpty()) continue;

            // ✅ Compute average (rounded to 3 decimals)
            $average = round($grades->avg('grade'), 3);

            // ✅ Round 89.5–89.9 up to 90
            if ($average >= 89.5 && $average < 90) {
                $average = 90.0;
            }

            // ❌ Skip if below 90 or has any subject grade <85
            if ($average < 90 || $grades->min('grade') < 85) continue;

            // 🧠 Determine rank
            if ($average >= 98) {
                $rank = 'With Highest Honors';
                $withHighestHonors++;
            } elseif ($average >= 95) {
                $rank = 'With High Honors';
                $withHighHonors++;
            } else {
                $rank = 'With Honors';
                $withHonors++;
            }

            $honorStudents[] = [
                'name' => "{$student->last_name}, {$student->first_name} {$student->middle_name}",
                'average' => $average,
                'rank' => $rank,
            ];
        }

        // 🧮 Sort from highest to lowest average
        $honorStudents = collect($honorStudents)
            ->sortByDesc('average')
            ->values()
            ->toArray();

        // 💾 Save to database (summary only)
        HonorRoll::create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'school_year' => $validated['school_year'],
            'quarter' => $validated['quarter'],
            'principal_name' => $validated['principal_name'],
            'with_honors_count' => $withHonors,
            'with_high_honors_count' => $withHighHonors,
            'with_highest_honors_count' => $withHighestHonors,
            'achievers_count' => 0,
        ]);

        // 🖨️ Generate PDF with ordered data
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.honor_roll', [
            'class' => $class,
            'teacher' => $teacher,
            'principal' => $validated['principal_name'],
            'quarter' => $validated['quarter'],
            'school_year' => $validated['school_year'],
            'honorStudents' => $honorStudents,
        ])->setPaper('A4', 'portrait');

        // 🎯 Direct download
        return $pdf->download("HonorRoll_{$class->name}_{$validated['quarter']}.pdf");
    }

    /** ❌ Delete honor roll record */
    public function destroy($id)
    {
        $honorRoll = HonorRoll::findOrFail($id);
        $honorRoll->delete();

        return back()->with('success', 'Honor Roll deleted successfully.');
    }


    /** 📥 Download stored PDF */
    public function download(HonorRoll $honorRoll)
    {
        $filePath = storage_path("app/public/{$honorRoll->pdf_path}");

        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        return response()->download($filePath);
    }
}
