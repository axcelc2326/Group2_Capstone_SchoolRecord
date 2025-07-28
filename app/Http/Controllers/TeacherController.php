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

    public function myStudents(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $class = $user->classTeaching;

        if (!$class) {
            return Inertia::render('Teacher/MyStudents', [
                'students' => [],
                'message' => 'You are not assigned to any class.',
            ]);
        }

        $query = $class->students()->where('approved_by_teacher', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%");
            });
        }

        $students = $query->orderBy('last_name')
            ->with('grades')
            ->paginate(40)
            ->withQueryString();

        return Inertia::render('Teacher/MyStudents', [
            'students' => $students,
            'filters' => ['search' => $search],
        ]);
    }

    public function viewStudentGrades($studentId)
    {
        $student = \App\Models\Student::with([
            'grades.subject',
            'class',
        ])->findOrFail($studentId);

        return Inertia::render('Teacher/ViewStudentGrades', [
            'student' => $student,
        ]);
    }
}
