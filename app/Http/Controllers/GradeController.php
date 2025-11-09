<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\GradeRemark; // ✅ Import it here
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function InputGrade($studentId)
    {
        $student = Student::with('class')->findOrFail($studentId);

        // ✅ Only fetch subjects for the student's grade level
        $subjects = Subject::where('grade_level', $student->class->grade_level)->get();

        // Load existing grades grouped by quarter and subject
        $grades = Grade::where('student_id', $studentId)
            ->get()
            ->groupBy('quarter')
            ->map(function ($quarterGrades) {
                return $quarterGrades->keyBy('subject_id')->map->grade;
            });

        return response()->json([
            'student' => $student,
            'subjects' => $subjects,
            'grades' => $grades,
            'currentQuarter' => 'Q1',
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id'   => 'required|exists:classes,id',
            'quarter'    => 'required|in:Q1,Q2,Q3,Q4',
            'grades'     => 'required|array',
            'grades.*'   => 'numeric|min:60|max:100',
        ]);

        $quarter = $request->quarter;

        // ✅ Save / update grades for this quarter
        foreach ($request->grades as $subjectId => $gradeValue) {
            Grade::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'subject_id' => $subjectId,
                    'quarter'    => $quarter,
                    'class_id'   => $request->class_id,
                ],
                [
                    'grade' => $gradeValue,
                ]
            );
        }

        // ✅ Check if all 4 quarters are filled for this student
        $totalSubjects = Subject::where('grade_level', function($q) use ($request) {
            $q->select('grade_level')
            ->from('classes')
            ->where('id', $request->class_id);
        })->count();

        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
        $allFilled = true;

        foreach ($quarters as $q) {
            $count = Grade::where('student_id', $request->student_id)
                ->where('class_id', $request->class_id)
                ->where('quarter', $q)
                ->count();

            if ($count < $totalSubjects) {
                $allFilled = false;
                break;
            }
        }

        // ✅ If all quarters filled, compute final average & insert grade remark
        if ($allFilled) {
            $average = Grade::where('student_id', $request->student_id)
                ->where('class_id', $request->class_id)
                ->avg('grade');

            // ✅ Round off the average (.5-.9 rounds up, .1-.4 rounds down)
            $roundedAverage = round($average);
            
            $remarks = $roundedAverage >= 75 ? 'Promoted' : 'Retained';

            GradeRemark::updateOrCreate(
                [
                    'student_id'    => $request->student_id,
                    'class_id'      => $request->class_id,
                ],
                [
                    'final_average' => $roundedAverage, // Use rounded value
                    'remarks'       => $remarks,
                ]
            );
        }

        return redirect()->back()->with('success', "Grades saved for {$quarter} quarter.");
    }

    /**
     * View grades (for parents)
     */
    public function viewGrades()
    {
        $parent = Auth::user();

        // ✅ Load student's class, subjects, grades, and latest grade remark
        $students = Student::with([
            'class',
            'grades' => fn($q) => $q->orderBy('quarter'),
            'grades.subject',
            'gradeRemarks' => fn($q) => $q->latest(), // Load latest remark
        ])->where('parent_id', $parent->id)->get()
        ->map(function ($student) {
            $latestRemark = $student->gradeRemarks->first(); // latest() loads newest first

            return [
                'id'             => $student->id,
                'name'           => trim(($student->first_name ?? '') . ' ' . ($student->middle_name ?? '') . ' ' . ($student->last_name ?? '')),
                'class'          => $student->class?->name,
                'class_grade_level'=> $student->class?->grade_level,
                'grades'         => $student->grades->map(function ($grade) {
                    return [
                        'subject' => $grade->subject?->name,
                        'quarter' => $grade->quarter,
                        'grade'   => round($grade->grade), // Removed decimal
                    ];
                }),
                'final_average'  => $latestRemark->final_average ?? round($student->grades->avg('grade') ?? 0), // Removed decimal
                'remarks'        => $latestRemark->remarks ?? 'In Progress',
            ];
        });

        return Inertia::render('Parent/ViewGrades', [
            'students' => $students,
        ]);
    }
}
