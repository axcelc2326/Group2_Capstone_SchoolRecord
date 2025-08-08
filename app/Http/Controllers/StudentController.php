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

        // Only students registered by the currently logged-in parent
        $students = Student::with('class')
            ->where('parent_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Parent/Create', [
            'classes' => $classes,
            'students' => $students,
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

        return redirect()->route('students.create')->with('success', 'Student registered!');
    }

    public function update(Request $request, $id)
    {
        $student = Student::where('id', $id)->where('parent_id', auth()->id())->firstOrFail();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
        ]);

        $student->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'class_id' => $request->class_id,
        ]);

        return redirect()->route('students.create')->with('message', 'Student updated successfully.');
    }

    public function destroy($id)
    {
        $student = Student::where('id', $id)->where('parent_id', auth()->id())->firstOrFail();
        $student->delete();

        return back()->with('message', 'Student deleted successfully.');
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
