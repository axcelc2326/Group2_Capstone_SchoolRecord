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

            // ✅ Get subjects for this grade level from preloaded subjects
            $subjects = $allSubjects->get($gradeLevel, collect());

            foreach ($subjects as $subject) {
                // ✅ Collect all grades for this subject
                $grades = $classes->flatMap->students
                    ->flatMap->grades
                    ->where('subject_id', $subject->id)
                    ->pluck('grade')
                    ->filter(fn($grade) => is_numeric($grade) && $grade >= 0 && $grade <= 100); // Validate grades

                if ($grades->isNotEmpty()) {
                    $average = round($grades->avg(), 2);
                    $subjectAverages[$subject->name] = $average;
                    $subjectStats[$subject->name] = [
                        'average' => $average,
                        'count' => $grades->count(),
                        'min' => $grades->min(),
                        'max' => $grades->max()
                    ];
                }
            }

            // ✅ Get all remarks (case-insensitive check)
            $remarks = $classes->flatMap->students->flatMap->gradeRemarks;
            $promoted = $remarks->filter(fn($r) => strtolower(trim($r->remarks ?? '')) === 'promoted')->count();
            $retained = $remarks->filter(fn($r) => strtolower(trim($r->remarks ?? '')) === 'retained')->count();

            // ✅ Calculate overall statistics
            $totalStudents = $classes->sum(fn($c) => $c->students->count());
            $overallAverage = !empty($subjectAverages) ? round(collect($subjectAverages)->avg(), 2) : 0;

            // ✅ Determine top and low subjects
            $sortedSubjects = collect($subjectAverages)->sort();
            $topSubject = $sortedSubjects->sortDesc()->keys()->first();
            $lowSubject = $sortedSubjects->keys()->first();

            return [
                'grade_level' => $gradeLevel, // Keep as string to support K1, K2, etc.
                'subject_averages' => $subjectAverages,
                'subject_stats' => $subjectStats, // Additional stats if needed
                'top_subject' => $topSubject,
                'low_subject' => $lowSubject,
                'total_students' => $totalStudents,
                'promoted' => $promoted,
                'retained' => $retained,
                'overall_average' => $overallAverage,
                'subjects_count' => count($subjectAverages),
                'classes_count' => $classes->count(),
                // Add metadata for debugging
                'has_data' => !empty($subjectAverages),
                'last_updated' => now()->toISOString(),
            ];
        })->values()->filter(fn($item) => $item['has_data']); // Only include grades with data

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
                'overall_school_average' => $analytics->avg('overall_average'),
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

            // ✅ Fallback if final_average not yet stored
            $computedAverage = round($student->grades->avg('grade') ?? 0, 2);

            return [
                'id'            => $student->id,
                'name'          => $name,
                'class_name'    => $student->class->name ?? 'N/A', // ✅ Include class name
                'final_average' => $latestRemark->final_average ?? $computedAverage,
                'remarks'       => $latestRemark->remarks ?? 'In Progress',
            ];
        })->sortByDesc('final_average')->values()->all();

        return Inertia::render('Analytics/ClassStudents', [
            'grade_level' => $gradeLevel,
            'students'    => $students,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////////////////

    public function teacherAnalytics(Request $request)
    {
        $user = auth()->user();

        // ✅ Assuming your `ClassModel` has a `teacher_id` column
        $classes = ClassModel::with('students.grades')
            ->where('teacher_id', $user->id)
            ->get();

        $analytics = $classes->map(function ($class) {
            $subjectAverages = [];

            // ✅ Only fetch subjects for this class's grade level
            $subjects = Subject::where('grade_level', $class->grade_level)->get();

            foreach ($subjects as $subject) {
                $grades = $class->students->flatMap(function ($student) use ($subject) {
                    return $student->grades->where('subject_id', $subject->id)->pluck('grade');
                });

                $subjectAverages[$subject->name] = round($grades->avg() ?? 0, 2);
            }

            $topSubject = collect($subjectAverages)->sortDesc()->keys()->first();
            $lowSubject = collect($subjectAverages)->sort()->keys()->first();

            return [
                'class' => $class->name,
                'grade_level' => $class->grade_level,
                'subject_averages' => $subjectAverages,
                'top_subject' => $topSubject,
                'low_subject' => $lowSubject,
                'total_students' => $class->students->count(),
                'id' => $class->id,
                'subjects' => $subjects,
            ];
        });

        return Inertia::render('Analytics/TeacherIndex', [
            'analytics' => $analytics,
        ]);
    }
}
