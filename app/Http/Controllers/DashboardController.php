<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\Grade;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('roles');
        $role = $user->getRoleNames()->first();

        switch ($role) {
            case 'admin':
                // 🏆 Get top 3 performing classes
                $topClasses = ClassModel::with('students.grades')
                    ->get()
                    ->map(function ($class) {
                        $allGrades = $class->students->flatMap->grades;
                        $average = $allGrades->avg('grade') ?? 0;
                        return [
                            'id' => $class->id,
                            'name' => $class->name,
                            'grade_level' => $class->grade_level,
                            'average' => round($average, 2),
                        ];
                    })
                    ->sortByDesc('average')
                    ->take(3)
                    ->values(); // Ensure it's a clean array for Vue

                // 📊 Summary info
                $summary = [
                    'total_classes' => ClassModel::count(),
                    'total_students' => User::role('parent')->count(),
                    'total_teachers' => User::role('teacher')->count(),
                    'overall_average' => round(Grade::avg('grade') ?? 0, 2),
                ];

                // 📢 Latest announcements
                $announcements = Announcement::with('creator:id,name')
                ->latest()
                ->take(3)
                ->get(['id', 'title', 'body', 'created_at', 'created_by'])
                ->map(function ($announcement) {
                    return [
                        'id' => $announcement->id,
                        'title' => $announcement->title,
                        'body' => $announcement->body,
                        'created_at' => $announcement->created_at,
                        'created_by' => $announcement->creator->name ?? 'Unknown',
                    ];
                });

                // 🎯 Send to Inertia Vue page
                return Inertia::render('Admin/Dashboard', [
                    'topClasses' => $topClasses,
                    'summary' => $summary,
                    'announcements' => $announcements,
                ]);

            case 'teacher':
                return Inertia::render('Teacher/Dashboard', ['user' => $user]);

            case 'parent':
                return Inertia::render('Parent/Dashboard', ['user' => $user]);

            default:
                abort(403, 'Unauthorized');
        }
    }
}
