<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Subject;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function create($studentId)
    {
        $student = Student::with('class')->findOrFail($studentId);

        // ✅ Only fetch subjects for the student's grade level
        $subjects = Subject::where('grade_level', $student->class->grade_level)->get();

        // Load existing grades grouped by quarter and subject
        $grades = Grade::where('student_id', $studentId)
            ->get()
            ->groupBy('quarter')
            ->map(function ($quarterGrades) {
                return $quarterGrades->keyBy('subject_id')->map->grade;
            });

        return Inertia::render('Teacher/GradeEntry', [
            'student' => $student,
            'subjects' => $subjects,
            'grades' => $grades,
            'currentQuarter' => 'Q1',
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'grades' => 'required|array',
            'grades.*' => 'numeric|min:60|max:100',
        ]);

        $quarter = $request->quarter;

        foreach ($request->grades as $subjectId => $gradeValue) {
            Grade::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'subject_id' => $subjectId,
                    'quarter' => $quarter,
                    'class_id' => $request->class_id,
                ],
                [
                    'grade' => $gradeValue,
                ]
            );
        }

        return redirect()->route('teacher.students')->with('success', "Grades saved for {$quarter} quarter.");
    }

    /**
     * View grades (for parents)
     */
    public function viewGrades()
    {
        $parent = Auth::user();

        // Load parent’s students with their classes, subjects, and grades
        $students = Student::with([
            'class',
            'grades' => fn ($q) => $q->orderBy('quarter'),
            'grades.subject',
        ])->where('parent_id', $parent->id)->get();

        return Inertia::render('Parent/ViewGrades', [
            'students' => $students,
        ]);
    }
}
