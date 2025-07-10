<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassModel; // assuming you use SchoolClass as model
use Inertia\Inertia;

class ClassController extends Controller
{
    // Show the form to create a new class
    public function create()
    {
        return Inertia::render('Classes/Create');
    }

    // Store the new class
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

        return redirect()->route('dashboard')->with('success', 'Class created successfully!');
    }
}
