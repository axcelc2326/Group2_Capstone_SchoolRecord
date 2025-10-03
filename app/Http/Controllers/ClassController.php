<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClassModel;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $gradeLevel = $request->input('grade_level');

        $classes = ClassModel::with('teacher') // eager load teacher
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('grade_level', 'like', "%{$search}%");
                });
            })
            ->when($gradeLevel, function ($query, $gradeLevel) {
                $query->where('grade_level', $gradeLevel);
            })
            ->orderBy('grade_level', 'asc')
            ->paginate(10)
            ->withQueryString();

        // get all teachers
        $teachers = User::role('teacher')->select('id', 'name')->get();

        return Inertia::render('Admin/CreateClass', [
            'classes' => $classes,
            'filters' => [
                'search' => $search,
                'grade_level' => $gradeLevel,
            ],
            'totalCount' => ClassModel::count(),
            'teachers' => $teachers, // ✅ pass teachers to Vue
        ]);
    }

    // ✅ Store the new class
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name',
            'grade_level' => 'required|string|in:K1,K2,1,2,3,4,5,6',
        ]);

        ClassModel::create([
            'name' => $request->name,
            'grade_level' => $request->grade_level,
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully!');
    }

    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes', 'name')->ignore($class->id), // ✅ allow same name for this record
            ],
            'grade_level' => 'required|string|in:K1,K2,1,2,3,4,5,6',
        ]);

        $class->update([
            'name' => $request->name,
            'grade_level' => $request->grade_level,
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully!');
    }

    // ✅ Delete a class
    public function destroy($id)
    {
        $class = ClassModel::with('students.grades', 'students.gradeRemarks')->findOrFail($id);

        foreach ($class->students as $student) {
            // Clear related grades and remarks
            $student->grades()->delete();
            $student->gradeRemarks()->delete();

            // Unassign student from class
            $student->class_id = null;
            $student->save();
        }

        // Delete the class itself
        $class->delete();

        return back()->with('success', 'Class deleted, students unassigned, and related data cleared successfully!');
    }
}
