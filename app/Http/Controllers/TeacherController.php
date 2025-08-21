<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TeacherController extends Controller
{
    // View all unapproved students under the teacher's class
    public function approveStudents()
    {
        $teacherId = Auth::id();

        $students = Student::with(['class', 'parent']) // Load parent and class
            ->whereHas('class', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->where('approved_by_teacher', false)
            ->get();

        // Add parent_name for each student
        $students->transform(function ($student) {
            $student->parent_name = $student->parent
                ? $student->parent->name // parent is a User, use 'name' field
                : 'N/A';
            return $student;
        });

        // Count of pending students
        $pendingCount = $students->count();

        return Inertia::render('Teacher/ApproveStudents', [
            'students' => $students,
            'pendingCount' => $pendingCount,
        ]);
    }

    // ✅ Teacher approval method
    public function approveStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->approved_by_teacher = true;
        $student->save();

        return back()->with('message', 'Student approved.');
    }

    public function denyStudent($studentId)
    {
        $student = Student::findOrFail($studentId);

        // Remove from class and unapprove
        $student->update([
            'class_id' => null,
            'approved_by_teacher' => false,
        ]);

        return redirect()->back()->with('success', 'Student has been denied and removed from your class.');
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
            ->with('grades', 'parent') // load parent relationship
            ->paginate(40)
            ->withQueryString();

        $students->getCollection()->transform(function ($student) {
            $student->parent_name = $student->parent
                ? $student->parent->name // use 'name' instead of first_name + last_name
                : 'N/A';
            return $student;
        });

        return Inertia::render('Teacher/MyStudents', [
            'students' => $students,
            'filters' => ['search' => $search],
        ]);
    }

    public function viewStudentGrades($studentId)
    {
        $student = Student::with([
            'grades.subject',
            'class',
        ])->findOrFail($studentId);

        // ✅ Only fetch subjects for the student's grade level
        $subjects = Subject::where('grade_level', $student->class->grade_level)->get();

        // ✅ Group grades by quarter → subject_id → grade
        $grades = $student->grades
            ->groupBy('quarter')
            ->map(function ($quarterGrades) {
                return $quarterGrades->keyBy('subject_id')->map->grade;
            })
            ->toArray(); // ✅ convert to plain array for Inertia

        return Inertia::render('Teacher/ViewStudentGrades', [
            'student' => $student,
            'subjects' => $subjects,
            'grades'   => $grades, // ✅ added grouped grades
        ]);
    }
}
