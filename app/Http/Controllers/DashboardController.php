<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\Grade;
use App\Models\Announcement;
use App\Models\Student;
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
                    'total_students' => Student::count(),
                    'total_teachers' => User::role('teacher')->count(),
                    'total_parents' => User::role('parent')->count(),
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
    $parentId = $user->id;

    // 📚 Get all students linked to this parent (with grades & class info)
    $students = Student::with('grades', 'class')
        ->where('parent_id', $parentId)
        ->get();

    // 🧮 Student stats
    $totalRegistered = $students->count();
    $totalEnrolled = $students->where('approved_by_teacher', 1)->count();
    $totalPending = $students->where('approved_by_teacher', 0)->count();

    // 📊 Average grade (overall) - exclude pending students
    $enrolledGrades = $students
        ->where('approved_by_teacher', 1)
        ->flatMap->grades;
    $averageGrade = round($enrolledGrades->avg('grade') ?? 0, 2);

    // 👦 Individual child performance with class name & grade level
    $childrenPerformance = $students->map(function ($student) {
        $className = $student->class->name ?? null;
        $gradeLevel = $student->class->grade_level ?? null;

        if ($student->approved_by_teacher != 1) {
            return [
                'name' => "{$student->first_name} {$student->last_name}",
                'class_name' => $className ?? 'No Class Assigned',
                'grade_level' => $gradeLevel ?? 'No Grade Level',
                'average' => null,
                'status' => 'Pending Approval',
            ];
        }

        $avg = round($student->grades->avg('grade') ?? 0, 2);
        return [
            'name' => "{$student->first_name} {$student->last_name}",
            'class_name' => $className ?? 'No Class Assigned',
            'grade_level' => $gradeLevel ?? 'No Grade Level',
            'average' => $avg,
            'status' => $avg >= 75 ? 'Doing Good' : 'Terrible',
        ];
    });

    // 📢 Latest announcements (class-specific + global)
    $classIds = $students->pluck('class_id')->unique();
    $announcements = Announcement::with('creator:id,name')
        ->where(function ($query) use ($classIds) {
            $query->whereIn('class_id', $classIds)
                ->orWhereNull('class_id'); // Include global announcements
        })
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

    return Inertia::render('Parent/Dashboard', [
        'user' => $user,
        'summary' => [
            'total_registered' => $totalRegistered,
            'total_enrolled' => $totalEnrolled,
            'total_pending' => $totalPending,
            'average_grade' => $averageGrade,
        ],
        'childrenPerformance' => $childrenPerformance,
        'announcements' => $announcements,
    ]);

            default:
                abort(403, 'Unauthorized');
        }
    }
}
