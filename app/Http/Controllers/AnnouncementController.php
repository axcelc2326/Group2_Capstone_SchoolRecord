<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    // Show form (admin/teacher only)
    public function create()
    {
        return Inertia::render('Announcement/Create');
    }

    // Store announcement (no longer needs class_id)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Announcement::create([
            'title' => $request->title,
            'body' => $request->body,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Announcement created successfully!');
    }

    // View announcements (for all parents)
    public function index()
    {
        $announcements = Announcement::latest()->get();

        return inertia('Parent/Announcements', [
            'announcements' => $announcements
        ]);
    }
}
