<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    // Show the assign teacher form
    public function assignTeacherForm(Request $request)
    {
        $teachers = User::role('teacher')->get(['id', 'name']);
        $classes = ClassModel::with('teacher')->get(); // for the dropdown

        // ✅ Paginate the class-teacher assignments (10 per page)
        $assignments = ClassModel::with('teacher')
            ->orderBy('grade_level')
            ->paginate(10)
            ->withQueryString(); // keeps query params like page on pagination links

        return Inertia::render('Admin/AssignTeacher', [
            'teachers' => $teachers,
            'classes' => $classes,
            'assignments' => $assignments, // pass to Vue
        ]);
    }

    // Handle form submission
    public function assignTeacher(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $class = ClassModel::findOrFail($request->class_id);
        $class->teacher_id = $request->teacher_id;
        $class->save();

        return redirect()->route('dashboard')->with('success', 'Teacher assigned successfully!');
    }
}
