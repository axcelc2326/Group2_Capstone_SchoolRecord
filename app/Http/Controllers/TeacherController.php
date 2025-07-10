<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TeacherController extends Controller
{
    // View all unapproved students under the teacher's class
    public function approveStudents()
    {
        $teacherId = Auth::id();

        $students = Student::with('class')
            ->whereHas('class', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->where('approved_by_teacher', false)
            ->get();

        return Inertia::render('Teacher/ApproveStudents', [
            'students' => $students
        ]);
    }

    public function myStudents()
    {
        $user = Auth::user();

        // Get the class where this teacher is assigned
        $class = $user->classTeaching;

        if (!$class) {
            return Inertia::render('Teacher/MyStudents', [
                'students' => [],
                'message' => 'You are not assigned to any class.',
            ]);
        }

        $students = $class->students()->with('grades')->get();

        return Inertia::render('Teacher/MyStudents', [
            'students' => $students,
        ]);
    }
}
