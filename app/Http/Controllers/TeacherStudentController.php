<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Grade;
use App\Models\GradeRemark;
use App\Models\ClassModel; // Ensure this model exists for your classes
use App\Models\SF5;
use App\Models\HonorRoll;

class TeacherStudentController extends Controller
{
    public function promoteStudents(Request $request)
    {
        $request->validate([
            'target_class_id' => 'required|exists:classes,id',
        ]);

        $teacherId = auth()->id();
        $targetClassId = $request->input('target_class_id');

        try {
            $promotedCount = 0;

            DB::transaction(function () use ($teacherId, $targetClassId, &$promotedCount) {
                // Get all students handled by this teacher who have grade remarks
                $students = Student::whereHas('class', function ($query) use ($teacherId) {
                        $query->where('teacher_id', $teacherId);
                    })
                    ->whereHas('gradeRemarks')
                    ->get();

                $promotedCount = $students->count();

                if ($promotedCount > 0) {
                    // Get all student IDs for cleanup
                    $studentIds = $students->pluck('id');

                    // ✅ Bulk promote students
                    Student::whereIn('id', $studentIds)->update([
                        'class_id' => $targetClassId,
                        'approved_by_teacher' => true,
                    ]);

                    // ✅ Bulk delete all grades for promoted students
                    Grade::whereIn('student_id', $studentIds)->delete();
                    
                    // ✅ Bulk delete all grade remarks for promoted students
                    GradeRemark::whereIn('student_id', $studentIds)->delete();

                    // ✅ Delete the teacher's SF5 and HonorRoll records for their current classes
                    $classIds = $students->pluck('class_id')->unique();
                    SF5::whereIn('class_id', $classIds)
                        ->where('teacher_id', $teacherId)
                        ->delete();

                    HonorRoll::whereIn('class_id', $classIds)
                        ->where('teacher_id', $teacherId)
                        ->delete();
                    
                    \Log::info("{$promotedCount} students promoted to class {$targetClassId}, grades, remarks, SF5, and HonorRoll cleared");
                }
            });

            if ($promotedCount > 0) {
                return back()->with('success', "{$promotedCount} students with grade remarks have been promoted to the new class. All grades, grade remarks, SF5, and HonorRoll records have been cleared.");
            } else {
                return back()->with('error', 'No students with grade remarks found to promote.');
            }

        } catch (\Exception $e) {
            \Log::error('Promotion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to promote students: ' . $e->getMessage());
        }
    }

    /** ✅ Unapprove all students in a class and remove their class assignment */
    public function unapproveAll(Request $request)
    {
        $teacherId = auth()->id();

        $students = Student::whereHas('class', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->get();

        foreach ($students as $student) {
            // Unapprove student
            $student->approved_by_teacher = false;

            // Remove class assignment
            $student->class_id = null;
            $student->save();

            // Delete all grades
            $student->grades()->delete();

            // ✅ Delete grade remarks
            $student->gradeRemarks()->delete();
        }

        // ✅ Delete the teacher's SF5 and HonorRoll records for all their classes
        $classIds = ClassModel::where('teacher_id', $teacherId)->pluck('id');
        SF5::whereIn('class_id', $classIds)->delete();
        HonorRoll::whereIn('class_id', $classIds)->delete();

        return back()->with('message', 'All students unapproved, grades, grade remarks, SF5, and HonorRoll records cleared, and class assignments removed.');
    }

    /** ✅ Clear all grades (and grade remarks) for all students in a class */
    public function clearAllGrades(Request $request)
    {
        $teacherId = auth()->id();

        // Get all student IDs handled by this teacher
        $studentIds = Student::whereHas('class', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->pluck('id');

        // Delete all grades for those students
        Grade::whereIn('student_id', $studentIds)->delete();

        // ✅ Delete all grade remarks for those students
        GradeRemark::whereIn('student_id', $studentIds)->delete();

        // ✅ Delete the teacher's SF5 and HonorRoll records for their classes
        $classIds = ClassModel::where('teacher_id', $teacherId)->pluck('id');
        SF5::whereIn('class_id', $classIds)->delete();
        HonorRoll::whereIn('class_id', $classIds)->delete();

        return back()->with('message', 'All grades, grade remarks, SF5, and HonorRoll records cleared.');
    }

    // ✅ Unapprove a single student, clear their grades and remarks, and remove class assignment
    public function unapproveStudent(Student $student)
    {
        $student->approved_by_teacher = false;
        $student->class_id = null;
        $student->save();

        // Delete all grades
        $student->grades()->delete();

        // ✅ Delete grade remarks if any
        $student->gradeRemarks()->delete();

        return back()->with('success', 'Student unapproved, grades and grade remarks cleared, and removed from class.');
    }

    // ✅ Clear grades (and grade remarks) for a single student
    public function clearGrades($studentId)
    {
        $student = Student::findOrFail($studentId);

        // Delete grades
        Grade::where('student_id', $student->id)->delete();

        // ✅ Delete grade remarks as well
        GradeRemark::where('student_id', $student->id)->delete();

        return back()->with('message', 'Grades and grade remarks cleared for the student.');
    }
    
    public function getAvailableClasses()
    {
        $teacherId = auth()->id();
        
        // Get all classes except the current teacher's classes
        $availableClasses = ClassModel::where('teacher_id', '!=', $teacherId)
            ->orWhereNull('teacher_id')
            ->get(['id', 'name', 'grade_level'])
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'grade_level' => $class->grade_level,
                ];
            });
        
        return response()->json($availableClasses);
    }
}