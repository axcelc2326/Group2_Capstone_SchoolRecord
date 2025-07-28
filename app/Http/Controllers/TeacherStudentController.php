<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Grade;
use App\Models\ClassModel; // Ensure this model exists for your classes

class TeacherStudentController extends Controller
{
    // ✅ Unapprove all students in a class
    public function unapproveAll(Request $request)
    {
        $teacherId = auth()->id();

        Student::whereHas('class', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->update(['approved_by_teacher' => false]);

        return back()->with('message', 'All students unapproved.');
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

    // ✅ Unapprove single student
    public function unapproveStudent(Student $student)
    {
        $student->update(['approved_by_teacher' => false]);
        return back()->with('success', 'Student unapproved.');
    }

    // ✅ Clear grades for a single student
    public function clearGrades($studentId)
    {
        $student = Student::findOrFail($studentId);

        Grade::where('student_id', $student->id)->delete();

        return back()->with('message', 'Grades cleared for the student.');
    }
}
