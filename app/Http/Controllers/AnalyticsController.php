<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\ClassModel;
use App\Models\GradeRemark;
use App\Models\Subject; // ✅ Import Subject
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 1;

        // ✅ Preload classes, students, grades, and remarks
        $classes = ClassModel::with([
            'students.grades.subject', // Include subject relationship for better data access
            'students.gradeRemarks',
        ])->get();

        // ✅ Preload all subjects and group by grade_level
        $allSubjects = Subject::all()->groupBy('grade_level');

        // ✅ Group classes by grade level
        $grouped = $classes->groupBy('grade_level');

        $analytics = $grouped->map(function ($classes, $gradeLevel) use ($allSubjects) {
            $subjectAverages = [];
            $subjectStats = [];

            // ✅ Collect class names for this grade level
            $classNames = $classes->pluck('name')->all(); // or 'class_name' if that's the column

            // ✅ Get subjects for this grade level
            $subjects = $allSubjects->get($gradeLevel, collect());

            foreach ($subjects as $subject) {
                $grades = $classes->flatMap->students
                    ->flatMap->grades
                    ->where('subject_id', $subject->id)
                    ->pluck('grade')
                    ->filter(fn($grade) => is_numeric($grade) && $grade >= 0 && $grade <= 100);

                if ($grades->isNotEmpty()) {
                    $average = round($grades->avg()); // Removed decimal places
                    $subjectAverages[$subject->name] = $average;
                    $subjectStats[$subject->name] = [
                        'average' => $average, // Removed decimal places
                        'count' => $grades->count(),
                        'min' => round($grades->min()), // Removed decimal places
                        'max' => round($grades->max()) // Removed decimal places
                    ];
                }
            }

            $remarks = $classes->flatMap->students->flatMap->gradeRemarks;
            $promoted = $remarks->filter(fn($r) => strtolower(trim($r->remarks ?? '')) === 'promoted')->count();
            $retained = $remarks->filter(fn($r) => strtolower(trim($r->remarks ?? '')) === 'retained')->count();

            $totalStudents = $classes->sum(fn($c) => $c->students->count());
            $overallAverage = !empty($subjectAverages) ? round(collect($subjectAverages)->avg()) : 0; // Removed decimal places

            $sortedSubjects = collect($subjectAverages)->sort();
            $topSubject = $sortedSubjects->sortDesc()->keys()->first();
            $lowSubject = $sortedSubjects->keys()->first();

            return [
                'grade_level' => $gradeLevel,
                'class_names' => $classNames, // ✅ added here
                'subject_averages' => $subjectAverages,
                'subject_stats' => $subjectStats,
                'top_subject' => $topSubject,
                'low_subject' => $lowSubject,
                'total_students' => $totalStudents,
                'promoted' => $promoted,
                'retained' => $retained,
                'overall_average' => $overallAverage, // Already rounded above
                'subjects_count' => count($subjectAverages),
                'classes_count' => $classes->count(),
                'has_data' => !empty($subjectAverages),
                'last_updated' => now()->toISOString(),
            ];
        })->values()->filter(fn($item) => $item['has_data']);

        // ✅ Manual Pagination
        $page = max(1, (int) $request->get('page', 1));
        $total = $analytics->count();
        $lastPage = max(1, ceil($total / $perPage));
        $page = min($page, $lastPage); // Ensure page doesn't exceed last page
        
        $paginated = $analytics->forPage($page, $perPage);

        return Inertia::render('Analytics/Index', [
            'analytics' => $paginated->values()->all(),
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
                'from' => $total > 0 ? (($page - 1) * $perPage) + 1 : 0,
                'to' => min($page * $perPage, $total),
                'next_page_url' => $page < $lastPage ? url()->current() . '?page=' . ($page + 1) : null,
                'prev_page_url' => $page > 1 ? url()->current() . '?page=' . ($page - 1) : null,
                'path' => url()->current(),
                'has_more_pages' => $page < $lastPage,
            ],
            // Add summary data
            'summary' => [
                'total_grade_levels' => $total,
                'total_students' => $analytics->sum('total_students'),
                'total_promoted' => $analytics->sum('promoted'),
                'total_retained' => $analytics->sum('retained'),
                'overall_school_average' => round($analytics->avg('overall_average')), // Removed decimal places
            ]
        ]);
    }

    public function showGradeLevelStudents($gradeLevel)
    {
        $classes = ClassModel::with([
            'students.user',
            'students.grades',
            'students.gradeRemarks',
            'students.parent',
        ])->where('grade_level', $gradeLevel)->get();

        $students = $classes->flatMap->students->map(function ($student) {
            $name = optional($student->parent)->fname
                ? trim($student->parent->fname . ' ' . $student->parent->lname)
                : trim($student->first_name . ' ' . $student->last_name);

            // ✅ Latest computed remark
            $latestRemark = $student->gradeRemarks->sortByDesc('created_at')->first();

            // ✅ Fallback if final_average not yet stored - removed decimal
            $computedAverage = round($student->grades->avg('grade') ?? 0);

            return [
                'id'            => $student->id,
                'name'          => $name,
                'class_name'    => $student->class->name ?? 'N/A', // ✅ Include class name
                'final_average' => $latestRemark->final_average ?? $computedAverage,
                'remarks'       => $latestRemark->remarks ?? 'In Progress',
            ];
        })->sortByDesc('final_average')->values()->all();

        return Inertia::render('Analytics/GradeStudents', [
            'grade_level' => $gradeLevel,
            'students'    => $students,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////////////////

    public function teacherAnalytics(Request $request)
    {
        $user = auth()->user();

        // ✅ Get only the teacher's assigned classes, with students, grades, and remarks
        $classes = ClassModel::with([
            'students.grades.subject',
            'students.gradeRemarks',
        ])
            ->where('teacher_id', $user->id)
            ->get();

        // ✅ Preload all subjects grouped by grade level
        $allSubjects = Subject::all()->groupBy('grade_level');

        // ✅ Compute analytics per class
        $analytics = $classes->map(function ($class) use ($allSubjects) {
            $subjectAverages = [];
            $subjectStats = [];

            $gradeLevel = $class->grade_level;
            $subjects = $allSubjects->get($gradeLevel, collect());

            foreach ($subjects as $subject) {
                $grades = $class->students
                    ->flatMap->grades
                    ->where('subject_id', $subject->id)
                    ->pluck('grade')
                    ->filter(fn($grade) => is_numeric($grade) && $grade >= 0 && $grade <= 100);

                if ($grades->isNotEmpty()) {
                    $average = round($grades->avg()); // Removed decimal places
                    $subjectAverages[$subject->name] = $average;
                    $subjectStats[$subject->name] = [
                        'average' => $average,
                        'count'   => $grades->count(),
                        'min'     => round($grades->min()), // Removed decimal places
                        'max'     => round($grades->max()), // Removed decimal places
                    ];
                }
            }

            // ✅ Compute promoted and retained
            $remarks = $class->students->flatMap->gradeRemarks;
            $promoted = $remarks->filter(fn($r) => strtolower(trim($r->remarks ?? '')) === 'promoted')->count();
            $retained = $remarks->filter(fn($r) => strtolower(trim($r->remarks ?? '')) === 'retained')->count();

            $totalStudents = $class->students->count();
            $overallAverage = !empty($subjectAverages) ? round(collect($subjectAverages)->avg()) : 0; // Removed decimal places

            $sortedSubjects = collect($subjectAverages)->sort();
            $topSubject = $sortedSubjects->sortDesc()->keys()->first();
            $lowSubject = $sortedSubjects->keys()->first();

            return [
                'class_id'         => $class->id,
                'class_name'       => $class->name,
                'grade_level'      => $gradeLevel,
                'subject_averages' => $subjectAverages,
                'subject_stats'    => $subjectStats,
                'top_subject'      => $topSubject,
                'low_subject'      => $lowSubject,
                'total_students'   => $totalStudents,
                'promoted'         => $promoted,
                'retained'         => $retained,
                'overall_average'  => $overallAverage,
                'subjects_count'   => count($subjectAverages),
                'has_data'         => !empty($subjectAverages),
                'last_updated'     => now()->toISOString(),
            ];
        })->filter(fn($item) => $item['has_data'])->values();

        // ✅ Summary for teacher's dashboard
        $summary = [
            'total_classes'     => $analytics->count(),
            'total_students'    => $classes->sum(fn($c) => $c->students->count()),
            'total_promoted'    => $analytics->sum('promoted'),
            'total_retained'    => $analytics->sum('retained'),
            'overall_average'   => round($analytics->avg('overall_average')), // Removed decimal places
        ];

        return Inertia::render('Analytics/TeacherIndex', [
            'analytics' => $analytics,
            'summary'   => $summary,
        ]);
    }

    public function showTeacherClassStudents($classId)
    {
        $user = auth()->user();
        
        // ✅ Get the specific class that belongs to the teacher
        $class = ClassModel::with([
            'students.user',
            'students.grades',
            'students.gradeRemarks',
            'students.parent',
        ])
        ->where('teacher_id', $user->id)
        ->where('id', $classId)
        ->firstOrFail();

        $students = $class->students->map(function ($student) {
            $name = optional($student->parent)->fname
                ? trim($student->parent->fname . ' ' . $student->parent->lname)
                : trim($student->first_name . ' ' . $student->last_name);

            // ✅ Latest computed remark
            $latestRemark = $student->gradeRemarks->sortByDesc('created_at')->first();

            // ✅ Fallback if final_average not yet stored
            $computedAverage = round($student->grades->avg('grade') ?? 0);

            return [
                'id' => $student->id,
                'name' => $name,
                'class_name' => $student->class->name ?? 'N/A',
                'final_average' => $latestRemark->final_average ?? $computedAverage,
                'remarks' => $latestRemark->remarks ?? 'In Progress',
            ];
        })->sortByDesc('final_average')->values()->all();

        return Inertia::render('Analytics/TeacherClassStudents', [
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
                'grade_level' => $class->grade_level,
            ],
            'students' => $students,
        ]);
    }
}
