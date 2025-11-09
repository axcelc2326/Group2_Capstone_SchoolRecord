<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use App\Models\Grade;
use App\Models\Announcement;
use App\Models\GradeRemark;
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
                        'id'          => $class->id,
                        'name'        => $class->name,
                        'grade_level' => $class->grade_level,
                        'average'     => round($average), // Removed decimal places
                    ];
                })
                ->sortByDesc('average')
                ->take(5)
                ->values();

            // 📊 Summary info
            $summary = [
                'total_classes'   => ClassModel::count(),
                'total_students'  => Student::count(),
                'total_teachers'  => User::role('teacher')->count(),
                'total_parents'   => User::role('parent')->count(),
                'overall_average' => round(Grade::avg('grade') ?? 0), // Removed decimal places
            ];

            // ✅ Promotion & Retention
            $promoted = GradeRemark::where('remarks', 'Promoted')->count();
            $retained = GradeRemark::where('remarks', 'Retained')->count();

            // ✅ Honor percentages
            $studentsWithRemarks = GradeRemark::with('student')->get();
            $total = max($studentsWithRemarks->count(), 1); // avoid division by zero

            $withHonors        = $studentsWithRemarks->whereBetween('final_average', [90, 94.99])->count();
            $withHighHonors    = $studentsWithRemarks->whereBetween('final_average', [95, 97.99])->count();
            $withHighestHonors = $studentsWithRemarks->whereBetween('final_average', [98, 100])->count();
            $nonHonors         = $total - ($withHonors + $withHighHonors + $withHighestHonors);

            $honorPercentages = [
                'with_honors'         => round(($withHonors / $total) * 100, 2),
                'with_high_honors'    => round(($withHighHonors / $total) * 100, 2),
                'with_highest_honors' => round(($withHighestHonors / $total) * 100, 2),
                'non_honor'           => round(($nonHonors / $total) * 100, 2),
            ];

            // 🌟 Top 5 performing students
            $topStudents = Student::with('class', 'grades')
                ->get()
                ->map(function ($student) {
                    $average = $student->grades->avg('grade') ?? 0;

                    return [
                        'id'          => $student->id,
                        'name'        => $student->first_name . ' ' . $student->last_name,
                        'class'       => $student->class->name ?? '',
                        'grade_level' => $student->class->grade_level ?? '',
                        'average'     => round($average), // Removed decimal places
                    ];
                })
                ->sortByDesc('average')
                ->take(5)
                ->values();

            // 📢 Latest announcements
            $announcements = Announcement::active() // Add this to filter out expired announcements
                ->with('creator:id,name')
                ->latest()
                ->take(3)
                ->get(['id', 'title', 'body', 'created_at', 'created_by', 'expires_at']) // Add expires_at
                ->map(function ($announcement) {
                    return [
                        'id'         => $announcement->id,
                        'title'      => $announcement->title,
                        'body'       => $announcement->body,
                        'created_at' => $announcement->created_at->toDateString(),
                        'created_by' => $announcement->creator->name ?? 'Unknown',
                        'expires_at' => $announcement->expires_at, // Include expires_at
                    ];
                });

            // 🎯 Send to Inertia Vue page
            return Inertia::render('Admin/Dashboard', [
                'topClasses'       => $topClasses,
                'summary'          => $summary,
                'announcements'    => $announcements,
                'promoted'         => $promoted,
                'retained'         => $retained,
                'honorPercentages' => $honorPercentages,
                'topStudents'      => $topStudents,
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
            $classAverage = round($class->students->flatMap->grades->avg('grade'));

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
                $average = round($student->grades->avg('grade'));
                return [
                    'id' => $student->id,
                    'name' => "{$student->first_name} {$student->middle_name} {$student->last_name}",
                    'average' => $average,
                ];
            })->sortByDesc('average')->take(3)->values();

            // ✅ Latest 3 announcements (specific class + global)
            $announcements = \App\Models\Announcement::active() // Add this to filter out expired announcements
                ->with('creator:id,name')
                ->where(function ($query) use ($class) {
                    $query->where('class_id', $class->id)
                        ->orWhereNull('class_id');
                })
                ->latest()
                ->take(3)
                ->get(['id', 'title', 'body', 'created_at', 'created_by', 'expires_at']) // Add expires_at
                ->map(function ($announcement) {
                    return [
                        'id' => $announcement->id,
                        'title' => $announcement->title,
                        'body' => $announcement->body,
                        'created_at' => $announcement->created_at,
                        'created_by' => $announcement->creator->name ?? 'Unknown',
                        'expires_at' => $announcement->expires_at, // Include expires_at
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


            // Parent Dashboard
            case 'parent':
                $parentId = $user->id;

                // 📚 Get all APPROVED students linked to this parent (with grades, class & remarks)
                $students = Student::with([
                    'grades',
                    'class',
                    'gradeRemarks' => fn($q) => $q->latest(), // load latest remark
                ])
                    ->where('parent_id', $parentId)
                    ->where('approved_by_teacher', 1)
                    ->get();

                // 🧮 Student stats
                $totalRegistered = $students->count(); 
                $totalEnrolled = $totalRegistered; 

                // 👦 Individual child performance with class name, grade level, and remarks
                $childrenPerformance = $students->map(function ($student) {
                    $className = $student->class->name ?? 'No Class Assigned';
                    $gradeLevel = $student->class->grade_level ?? 'No Grade Level';
                    $avg = round($student->grades->avg('grade') ?? 0); // Removed decimal

                    // Get latest grade remark
                    $latestRemark = $student->gradeRemarks->first();
                    
                    // Check if there are any grades or remarks
                    $hasGrades = $student->grades->isNotEmpty();
                    $hasRemark = !is_null($latestRemark);
                    
                    // Determine final average and remarks
                    if (!$hasGrades && !$hasRemark) {
                        // No grades and no remarks yet - show "In Progress"
                        $finalAverage = 0;
                        $remarkText = 'In Progress';
                    } else {
                        $finalAverage = $latestRemark->final_average ?? $avg;
                        
                        // Use remark from GradeRemarks if available, otherwise calculate based on average
                        if ($hasRemark) {
                            $remarkText = $latestRemark->remarks ?? ($avg >= 75 ? 'Promoted' : 'Failed');
                        } else {
                            $remarkText = $avg >= 75 ? 'Promoted' : 'Failed';
                        }
                    }

                    return [
                        'name' => "{$student->first_name} {$student->middle_name} {$student->last_name}",
                        'class_name' => $className,
                        'grade_level' => $gradeLevel,
                        'average' => $avg,
                        'final_average' => round($finalAverage), // Removed decimal
                        'remarks' => $remarkText,
                        'status' => $remarkText,
                    ];
                });

                // 🧾 Count promoted and failed students FROM GRADE REMARKS
                $studentIds = $students->pluck('id');
                
                // Get promoted count from GradeRemarks
                $promotedCount = GradeRemark::whereIn('student_id', $studentIds)
                    ->where('remarks', 'Promoted')
                    ->count();

                // Get retained count from GradeRemarks (equivalent to failed)
                $failedCount = GradeRemark::whereIn('student_id', $studentIds)
                    ->where('remarks', 'Retained')
                    ->count();

                // 📢 Latest announcements (class-specific + global)
                $classIds = $students->pluck('class_id')->unique();
                $announcements = Announcement::active() // Add this line to filter out expired announcements
                    ->with('creator:id,name')
                    ->where(function ($query) use ($classIds) {
                        $query->whereIn('class_id', $classIds)
                            ->orWhereNull('class_id'); // Include global announcements
                    })
                    ->latest()
                    ->take(3)
                    ->get(['id', 'title', 'body', 'created_at', 'created_by', 'expires_at']) // Add expires_at
                    ->map(function ($announcement) {
                        return [
                            'id' => $announcement->id,
                            'title' => $announcement->title,
                            'body' => $announcement->body,
                            'created_at' => $announcement->created_at,
                            'created_by' => $announcement->creator->name ?? 'Unknown',
                            'expires_at' => $announcement->expires_at, // Include expires_at in the response
                        ];
                    });

                return Inertia::render('Parent/Dashboard', [
                    'user' => $user,
                    'summary' => [
                        'total_registered' => $totalRegistered,
                        'total_enrolled' => $totalEnrolled,
                        'promoted' => $promotedCount,   // ✅ From GradeRemarks
                        'failed' => $failedCount,       // ✅ From GradeRemarks (as Retained)
                    ],
                    'childrenPerformance' => $childrenPerformance,
                    'announcements' => $announcements,
                ]);



            default:
                abort(403, 'Unauthorized');
        }
    }
}
