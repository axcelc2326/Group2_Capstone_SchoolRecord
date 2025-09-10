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
            'class_id'   => 'required|exists:classes,id',
            'teacher_id' => 'nullable|exists:users,id', // ✅ allow null
        ]);

        // ✅ Only check uniqueness if a teacher is selected
        if ($request->teacher_id) {
            $alreadyAssigned = ClassModel::where('teacher_id', $request->teacher_id)
                ->where('id', '!=', $request->class_id)
                ->exists();

            if ($alreadyAssigned) {
                return redirect()
                    ->back()
                    ->withErrors(['teacher_id' => 'This teacher is already assigned to another class.']);
            }
        }

        // ✅ Assign or unassign teacher
        $class = ClassModel::findOrFail($request->class_id);
        $class->teacher_id = $request->teacher_id; // can now be null
        $class->save();

        return redirect()
            ->route('admin.assign-teacher')
            ->with('success', $request->teacher_id 
                ? 'Teacher assigned successfully!' 
                : 'Teacher unassigned successfully!');
    }
}
