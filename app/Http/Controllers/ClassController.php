<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassModel;
use Inertia\Inertia;

class ClassController extends Controller
{
    // ✅ Display all classes with pagination and form
    public function create(Request $request)
    {
        $classes = ClassModel::orderBy('grade_level', 'asc')->paginate(10);

        return Inertia::render('Classes/Create', [
            'classes' => $classes,
        ]);
    }

    // ✅ Store the new class
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
        ]);

        ClassModel::create([
            'name' => $request->name,
            'grade_level' => $request->grade_level,
        ]);

        return redirect()->route('classes.create')->with('success', 'Class created successfully!');
    }

    // ✅ Show edit form
    public function edit(ClassModel $class)
    {
        return Inertia::render('Classes/Edit', [
            'classItem' => $class,
        ]);
    }

    public function update(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
        ]);

        $class->update($validated);

        return redirect()->route('classes.create')->with('success', 'Class updated successfully.');
    }

    // ✅ Delete a class
    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        $class->delete();

        return redirect()->route('classes.create')->with('success', 'Class deleted successfully!');
    }
}
