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
        return Inertia::render('Announcement/AdminCreate');
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

        return redirect()->route('dashboard')->with('success', 'Global announcement posted.');
    }

    public function createTeacher()
    {
        $classes = auth()->user()->classes;
        return Inertia::render('Announcement/TeacherCreate', [
            'classes' => $classes,
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
            // ✅ Get class_ids of all the parent's children
            $classIds = $user->students->pluck('class_id')->toArray();

            // ✅ Paginate global and class-specific announcements
            $announcements = Announcement::with('creator')
                ->where(function ($query) use ($classIds) {
                    $query->whereNull('class_id')
                        ->orWhereIn('class_id', $classIds);
                })
                ->latest()
                ->paginate(5) // ✅ Pagination (5 per page)
                ->withQueryString();

            return Inertia::render('Announcement/Index', [
                'announcements' => $announcements,
                'classIds' => $classIds,
            ]);
        }

        // ✅ Admin and teachers see all announcements with pagination
        $announcements = Announcement::with('creator')
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('Announcement/Index', [
            'announcements' => $announcements,
        ]);
    }
}
