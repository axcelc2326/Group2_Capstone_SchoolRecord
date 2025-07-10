<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    // Show the assign teacher form
    public function assignTeacherForm()
    {
        $teachers = User::role('teacher')->get(['id', 'name']);
        $classes = ClassModel::with('teacher')->get();

        return Inertia::render('Admin/AssignTeacher', [
            'teachers' => $teachers,
            'classes' => $classes,
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
