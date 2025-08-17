<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    /**
     * Show all subjects
     */
    public function index(Request $request)
    {
        $query = Subject::query()->orderBy('grade_level');

        if ($request->has('grade_level') && $request->grade_level !== '') {
            $query->where('grade_level', $request->grade_level);
        }

        $subjects = $query->paginate(8)->withQueryString();

        return Inertia::render('Admin/Subjects', [
            'subjects' => $subjects,
            'filters' => $request->only('grade_level'),
        ]);
    }

    /**
     * Store a new subject
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
        ]);

        Subject::create($request->only('name', 'grade_level'));

        return redirect()->back()->with('success', 'Subject added successfully.');
    }

    /**
     * Update an existing subject
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|integer|min:1|max:12',
        ]);

        $subject->update($request->only('name', 'grade_level'));

        return redirect()->back()->with('success', 'Subject updated successfully.');
    }

    /**
     * Delete a subject
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->back()->with('success', 'Subject deleted successfully.');
    }
}
