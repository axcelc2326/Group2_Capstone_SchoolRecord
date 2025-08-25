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
            //Admin Dashboard
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


            //Teacher Dashboard
            case 'teacher':
            // ✅ Get teacher's assigned class (assuming 1 class per teacher)
            $class = ClassModel::with('students.grades')
                ->where('teacher_id', $user->id)
                ->first();

            if (!$class) {
                return Inertia::render('Teacher/Dashboard', [
                    'user' => $user,
                    'summary' => [
                        'total_students' => 0,
                        'pending_students' => 0, // added
                        'total_subjects' => 0,
                        'class_average' => 0,
                        'top_subject' => null,
                        'worst_subject' => null,
                    ],
                    'topStudents' => [],
                    'announcements' => [],
                ]);
            }

            // ✅ Total students
            $totalStudents = $class->students->count();

            // ✅ Pending students (not approved yet)
            $pendingStudents = $class->students->where('approved_by_teacher', false)->count();

            // ✅ Subjects for this grade level
            $subjects = \App\Models\Subject::where('grade_level', $class->grade_level)->get();
            $totalSubjects = $subjects->count();

            // ✅ Class average
            $classAverage = round($class->students->flatMap->grades->avg('grade') ?? 0, 2);

            // ✅ Subject performance (avg per subject)
            $subjectAverages = [];
            foreach ($subjects as $subject) {
                $grades = $class->students->flatMap(function ($student) use ($subject) {
                    return $student->grades->where('subject_id', $subject->id)->pluck('grade');
                });
                $subjectAverages[$subject->name] = round($grades->avg() ?? 0, 2);
            }

            $topSubject = collect($subjectAverages)->sortDesc()->keys()->first();
            $worstSubject = collect($subjectAverages)->sort()->keys()->first();

            // ✅ Top 3 students (by average)
            $topStudents = $class->students->map(function ($student) {
                $average = round($student->grades->avg('grade') ?? 0, 2);
                return [
                    'id' => $student->id,
                    'name' => "{$student->first_name} {$student->last_name}",
                    'average' => $average,
                ];
            })->sortByDesc('average')->take(3)->values();

            // ✅ Latest 3 announcements (specific class + global)
            $announcements = \App\Models\Announcement::with('creator:id,name')
                ->where(function ($query) use ($class) {
                    $query->where('class_id', $class->id)
                        ->orWhereNull('class_id');
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

            return Inertia::render('Teacher/Dashboard', [
                'user' => $user,
                'summary' => [
                    'total_students' => $totalStudents,
                    'pending_students' => $pendingStudents, // ✅ added
                    'total_subjects' => $totalSubjects,
                    'class_average' => $classAverage,
                    'top_subject' => $topSubject,
                    'worst_subject' => $worstSubject,
                ],
                'topStudents' => $topStudents,
                'announcements' => $announcements,
            ]);


            //Parent Dashboard
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
