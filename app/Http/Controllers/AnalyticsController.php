<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function index()
    {
        // ✅ Fetch classes with students and their grades
        $classes = ClassModel::with('students.grades')->get();

        $subjects = ['English', 'Filipino', 'Mathematics', 'Science', 'Araling Panlipunan', 'ESP', 'MAPEH', 'EPP/TLE'];

        $analytics = $classes->map(function ($class) use ($subjects) {

            $subjectAverages = [];

            foreach ($subjects as $subject) {
                // ✅ Flatten all grades of students in the class for this subject
                $grades = $class->students->flatMap(function($student) use ($subject) {
                    return $student->grades->where('subject', $subject)->pluck('grade');
                });

                $subjectAverages[$subject] = $grades->avg() ?? 0;
            }

            $topSubject = collect($subjectAverages)->sortDesc()->keys()->first();
            $lowSubject = collect($subjectAverages)->sort()->keys()->first();

            return [
                'class' => $class->name,
                'grade_level' => $class->grade_level,
                'subject_averages' => $subjectAverages,
                'top_subject' => $topSubject,
                'low_subject' => $lowSubject,
            ];
        });

        return Inertia::render('Analytics/Index', [
            'analytics' => $analytics,
        ]);
    }
}
