<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query()->orderBy('grade_level');

        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        $subjects = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/ManageSubjects', [
            'subjects' => $subjects,
            'filters' => $request->only('grade_level'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string|in:K1,K2,1,2,3,4,5,6',
        ]);

        Subject::create([
            'name' => $request->name,
            'grade_level' => $request->grade_level,
        ]);

        return redirect()->back()->with('success', 'Subject added successfully!');
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'required|string|in:K1,K2,1,2,3,4,5,6',
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
