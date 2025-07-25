<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function createAdmin()
    {
        $announcements = Announcement::where('created_by', auth()->id())
            ->latest()
            ->paginate(5);

        return Inertia::render('Announcement/AdminCreate', [
            'announcements' => $announcements,
        ]);
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Announcement::create([
            'title' => $request->title,
            'body' => $request->body,
            'class_id' => null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('announcements.create')->with('success', 'Global announcement posted.');
    }

    public function createTeacher()
    {
        $classes = auth()->user()->classes;

        $announcements = Announcement::where('created_by', auth()->id())
            ->latest()
            ->paginate(5);

        return Inertia::render('Announcement/TeacherCreate', [
            'classes' => $classes,
            'announcements' => $announcements,
        ]);
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $teacher = auth()->user();
        $class = $teacher->class;

        if (!$class) {
            return back()->withErrors(['class' => 'You are not assigned to any class.']);
        }

        Announcement::create([
            'title' => $request->title,
            'body' => $request->body,
            'class_id' => $class->id,
            'created_by' => $teacher->id,
        ]);

        return redirect()->route('dashboard')->with('success', 'Class announcement posted.');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('parent')) {
            // ✅ Parents see global and class announcements
            $classIds = $user->students->pluck('class_id')->toArray();

            $announcements = Announcement::with('creator')
                ->where(function ($query) use ($classIds) {
                    $query->whereNull('class_id')
                        ->orWhereIn('class_id', $classIds);
                })
                ->latest()
                ->paginate(5)
                ->withQueryString();

            return Inertia::render('Announcement/Index', [
                'announcements' => $announcements,
                'classIds' => $classIds,
            ]);
        }

        // ✅ Admins and teachers only see their own announcements
        $announcements = Announcement::with('creator')
            ->where('created_by', $user->id)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('Announcement/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function editAdmin($id)
    {
        $announcement = Announcement::where('id', $id)
            ->where('created_by', auth()->id()) // Ensure only the owner can edit
            ->firstOrFail();

        return Inertia::render('Announcement/EditAdmin', [
            'announcement' => $announcement,
        ]);
    }

     public function editTeacher($id)
    {
        $announcement = Announcement::where('id', $id)
            ->where('created_by', auth()->id()) // Ensure only the owner can edit
            ->firstOrFail();

        return Inertia::render('Announcement/EditTeacher', [
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $announcement = Announcement::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $announcement->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect()->route('announcements.create')->with('success', 'Announcement updated.');
    }

    public function destroy($id)
    {
        $announcement = Announcement::where('id', $id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }

}
