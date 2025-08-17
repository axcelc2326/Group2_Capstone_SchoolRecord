<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Grade;
use App\Models\ClassModel; // Ensure this model exists for your classes

class TeacherStudentController extends Controller
{
    // ✅ Unapprove all students in a class and remove their class assignment
    public function unapproveAll(Request $request)
    {
        $teacherId = auth()->id();

        $students = Student::whereHas('class', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->get();

        foreach ($students as $student) {
            // Unapprove
            $student->approved_by_teacher = false;
            // Remove from class
            $student->class_id = null;
            $student->save();

            // Delete all grades
            $student->grades()->delete();
        }

        return back()->with('message', 'All students unapproved, grades cleared, and class assignments removed.');
    }

    // ✅ Clear all grades for all students in a class
    public function clearAllGrades(Request $request)
    {
        $teacherId = auth()->id();

        $studentIds = Student::whereHas('class', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->pluck('id');

        Grade::whereIn('student_id', $studentIds)->delete();

        return back()->with('message', 'All grades cleared.');
    }

    // ✅ Unapprove a single student, clear their grades, and remove class assignment
    public function unapproveStudent(Student $student)
    {
        $student->approved_by_teacher = false;
        $student->class_id = null;
        $student->grades()->delete();
        $student->save();

        return back()->with('success', 'Student unapproved, grades cleared, and removed from class.');
    }

    // ✅ Clear grades for a single student
    public function clearGrades($studentId)
    {
        $student = Student::findOrFail($studentId);

        Grade::where('student_id', $student->id)->delete();

        return back()->with('message', 'Grades cleared for the student.');
    }
}