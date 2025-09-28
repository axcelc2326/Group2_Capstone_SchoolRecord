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
            'students.grades',
            'students.gradeRemarks',
        ])->get();

        // ✅ Preload all subjects and group by grade_level
        $allSubjects = Subject::all()->groupBy('grade_level');

        // ✅ Group classes by grade level
        $grouped = $classes->groupBy('grade_level');

        $analytics = $grouped->map(function ($classes, $gradeLevel) use ($allSubjects) {
            $subjectAverages = [];

            // ✅ Get subjects for this grade level from preloaded subjects
            $subjects = $allSubjects->get($gradeLevel, collect());

            foreach ($subjects as $subject) {
                // ✅ Collect all grades for this subject
                $grades = $classes->flatMap->students
                    ->flatMap->grades
                    ->where('subject_id', $subject->id)
                    ->pluck('grade');

                $subjectAverages[$subject->name] = round($grades->avg() ?? 0, 2);
            }

            // ✅ Get all remarks (case-insensitive check)
            $remarks = $classes->flatMap->students->flatMap->gradeRemarks;

            $promoted = $remarks->filter(fn($r) => strtolower($r->remarks) === 'promoted')->count();
            $retained = $remarks->filter(fn($r) => strtolower($r->remarks) === 'retained')->count();

            return [
                'grade_level'      => $gradeLevel,
                'subject_averages' => $subjectAverages,
                'top_subject'      => collect($subjectAverages)->sortDesc()->keys()->first(),
                'low_subject'      => collect($subjectAverages)->sort()->keys()->first(),
                'total_students'   => $classes->sum(fn($c) => $c->students->count()),
                'promoted'         => $promoted,
                'retained'         => $retained,
                'subjects'         => $subjects,
            ];
        })->values();

        // ✅ Manual Pagination
        $page = $request->get('page', 1);
        $paginated = $analytics->forPage($page, $perPage);

        return Inertia::render('Analytics/Index', [
            'analytics' => $paginated->values()->all(),
            'pagination' => [
                'current_page'  => $page,
                'last_page'     => ceil($analytics->count() / $perPage),
                'next_page_url' => $page < ceil($analytics->count() / $perPage) ? url()->current() . '?page=' . ($page + 1) : null,
                'prev_page_url' => $page > 1 ? url()->current() . '?page=' . ($page - 1) : null,
                'path'          => url()->current(),
            ],
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
                'final_average' => $latestRemark->final_average ?? $computedAverage,
                'remarks'       => $latestRemark->remarks ?? 'In Progress',
            ];
        })->sortByDesc('final_average')->values()->all();

        return Inertia::render('Analytics/ClassStudents', [
            'grade_level' => $gradeLevel,
            'students'    => $students,
        ]);
    }
}
