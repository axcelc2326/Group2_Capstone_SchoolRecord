<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 1;

        // ✅ Paginate classes with students and grades
        $classes = ClassModel::with('students.grades')->paginate($perPage);

        $subjectNames = [
            1 => 'English',
            2 => 'Filipino',
            3 => 'Mathematics',
            4 => 'Science',
            5 => 'Araling Panlipunan',
            6 => 'ESP',
            7 => 'MAPEH',
            8 => 'EPP/TLE'
        ];

        // ✅ Map class data
        $analytics = collect($classes->items())->map(function ($class) use ($subjectNames) {
            $subjectAverages = [];

            foreach ($subjectNames as $subjectId => $subjectName) {
                $grades = $class->students->flatMap(function ($student) use ($subjectId) {
                    return $student->grades->where('subject_id', $subjectId)->pluck('grade');
                });

                $subjectAverages[$subjectName] = round($grades->avg() ?? 0, 2);
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
                'id' => $class->id, // ✅ Add this line
            ];
        });

        // ✅ Send paginated links and analytics data
        return Inertia::render('Analytics/Index', [
            'analytics' => $analytics,
            'pagination' => [
                'current_page' => $classes->currentPage(),
                'last_page' => $classes->lastPage(),
                'next_page_url' => $classes->nextPageUrl(),
                'prev_page_url' => $classes->previousPageUrl(),
                'path' => $classes->path(),
            ],
        ]);
    }

    public function showClassStudents($id)
    {
        $class = ClassModel::with('students.user', 'students.grades')->findOrFail($id);

        $students = $class->students->map(function ($student) {
            $average = round($student->grades->avg('grade') ?? 0, 2);

            $name = optional($student->parent)->fname
                ? trim($student->parent->fname . ' ' . $student->parent->lname)
                : trim($student->first_name . ' ' . $student->last_name);

            return [
                'id' => $student->id,
                'name' => $name,
                'average' => $average,
            ];
        })->sortByDesc('average')->values()->all(); // Rank by average

        return Inertia::render('Analytics/ClassStudents', [
            'class_name' => $class->name,
            'grade_level' => $class->grade_level,
            'students' => $students,
        ]);
    }
}
