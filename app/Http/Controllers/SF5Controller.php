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
            'region'       => 'required|string|max:255',
            'division'     => 'required|string|max:255',
            'school_id'    => 'required|string|max:50',
            'school_name'  => 'required|string|max:255',
            'school_year'  => 'required|string|max:50',
            'school_head'  => 'required|string|max:255',
            'scc_chair'    => 'required|string|max:255',
            'class_id'     => 'required|integer',
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
                'school_head',
                'scc_chair'
            ]),
        ])->setPaper('legal', 'landscape');

        return $pdf->download('SF5_Report.pdf');
    }
}
