<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ClassModel;
use App\Models\Student;

class StudentController extends Controller
{
    // ✅ Show student registration form (for parents)
    public function create()
    {
        $classes = ClassModel::select('id', 'name', 'grade_level')->get();

        return Inertia::render('Students/Create', [
            'classes' => $classes,
        ]);
    }


    // ✅ Store the student after validation
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
        ]);

        Student::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'class_id' => $request->class_id,
            'parent_id' => auth()->id(), // assumes parent is logged in
        ]);

        return redirect()->route('dashboard')->with('success', 'Student registered!');
    }

    // ✅ Teacher approval method
    public function approve($id)
    {
        $student = Student::findOrFail($id);
        $student->approved_by_teacher = true;
        $student->save();

        return back()->with('message', 'Student approved.');
    }
}
