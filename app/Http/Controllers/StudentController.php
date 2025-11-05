<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ClassModel;
use App\Models\Student;

class StudentController extends Controller
{
    // ✅ Show student registration form (for parents)
    public function index()
    {
        $classes = ClassModel::select('id', 'name', 'grade_level')->get();

        // Only students registered by the currently logged-in parent
        $students = Student::with('class')
            ->where('parent_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Parent/MyStudent', [
            'classes' => $classes,
            'students' => $students,
        ]);
    }

    public function store(Request $request, $parent)
    {
        $request->validate([
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',  // ✅ Added middle_name
            'last_name'   => 'required|string|max:255',
            'lrn'         => 'required|digits:12|unique:students,lrn', // ✅ validate LRN
            'gender'      => 'required|in:male,female',               // ✅ validate gender
            'class_id'    => 'required|exists:classes,id',
        ]);

        Student::create([
            'first_name'          => $request->first_name,
            'middle_name'         => $request->middle_name, // ✅ Added
            'last_name'           => $request->last_name,
            'lrn'                 => $request->lrn,
            'gender'              => $request->gender,
            'class_id'            => $request->class_id,
            'parent_id'           => $parent,  // use the route parameter
            'approved_by_teacher' => true,
        ]);

        return back()->with('success', 'Student created successfully!');
    }

    public function update(Request $request, $parentId, $studentId)
    {
        // Find the student belonging to the given parent
        $student = Student::where('id', $studentId)
                        ->where('parent_id', $parentId)
                        ->firstOrFail();

        $request->validate([
            'first_name'  => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255', // ✅ Added middle_name
            'last_name'   => 'required|string|max:255',
            'lrn'         => 'required|digits:12|unique:students,lrn,' . $student->id, // ✅ allow same LRN for current student
            'gender'      => 'required|in:male,female',
            'class_id'    => 'required|exists:classes,id',
        ]);

        $student->update([
            'first_name'          => $request->first_name,
            'middle_name'         => $request->middle_name, // ✅ Added
            'last_name'           => $request->last_name,
            'lrn'                 => $request->lrn,
            'gender'              => $request->gender,
            'class_id'            => $request->class_id,
            'approved_by_teacher' => true, // always true
        ]);

        return back()->with('success', 'Student updated successfully!');
    }

    public function destroy($parentId, $studentId)
    {
        // 1️⃣ Find the student that belongs to the given parent
        $student = Student::where('id', $studentId)
            ->where('parent_id', $parentId)
            ->firstOrFail();

        // 2️⃣ Delete all related grades (from `grades` table)
        if ($student->grades()->exists()) {
            $student->grades()->delete();
        }

        // 3️⃣ Delete all related grade remarks (from `grade_remarks` table)
        if ($student->gradeRemarks()->exists()) {
            $student->gradeRemarks()->delete();
        }

        // 4️⃣ Finally, delete the student record
        $student->delete();

        return back()->with('message', 'Student and all related records deleted successfully.');
    }
}
