<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\GradeRemark;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ParentsWithChildrenSeeder extends Seeder
{
    public function run(): void
    {
        // Create Parent Role if not exists
        Role::firstOrCreate(['name' => 'parent']);

        // Define grade levels as strings (K1, K2, 1-6)
        $gradeLevels = ['K1', 'K2', '1', '2', '3', '4', '5', '6'];

        // Unique class names
        $classNames = ['Gold', 'Silver', 'Diamond', 'Emerald', 'Ruby', 'Sapphire', 'Platinum', 'Titanium'];

        // Create Classes for each grade level
        foreach ($gradeLevels as $index => $grade) {
            ClassModel::firstOrCreate([
                'grade_level' => $grade,
                'name' => $classNames[$index],
                'teacher_id' => null,
            ]);
        }

        $usedLrns = [];

        // Create Parents and Students
        for ($i = 1; $i <= 150; $i++) {
            $parent = User::create([
                'name' => "Parent $i",
                'email' => "parent$i@example.com",
                'password' => Hash::make('password'),
            ]);
            $parent->assignRole('parent');

            // Assign each parent to a random class
            $class = ClassModel::inRandomOrder()->first();

            // Generate unique random 12-digit LRN
            do {
                $lrn = (string) random_int(100000000000, 999999999999);
            } while (in_array($lrn, $usedLrns));

            $usedLrns[] = $lrn;

            $student = Student::create([
                'first_name' => "Student",
                'middle_name' => "M.",
                'last_name' => "$i",
                'class_id' => $class->id,
                'parent_id' => $parent->id,
                'approved_by_teacher' => true,
                'lrn' => $lrn,
                'gender' => $i % 2 === 0 ? 'Male' : 'Female',
            ]);

            // Fetch subjects for this student's grade level
            $subjects = Subject::where('grade_level', $class->grade_level)->get();

            // ✅ Insert grades for all subjects across 4 quarters
            foreach ($subjects as $subject) {
                foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                    Grade::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'quarter'    => $quarter,
                            'class_id'   => $class->id,
                        ],
                        [
                            'grade' => rand(75, 99),
                        ]
                    );
                }
            }

            // ✅ Check if all quarters filled
            $totalSubjects = $subjects->count();
            $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
            $allFilled = true;

            foreach ($quarters as $q) {
                $count = Grade::where('student_id', $student->id)
                    ->where('class_id', $class->id)
                    ->where('quarter', $q)
                    ->count();

                if ($count < $totalSubjects) {
                    $allFilled = false;
                    break;
                }
            }

            // ✅ If all quarters filled, compute final average & insert grade remark
            if ($allFilled) {
                $average = Grade::where('student_id', $student->id)
                    ->where('class_id', $class->id)
                    ->avg('grade');

                $remarks = $average >= 75 ? 'Promoted' : 'Retained';

                GradeRemark::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'class_id'   => $class->id,
                    ],
                    [
                        'final_average' => $average,
                        'remarks'       => $remarks,
                    ]
                );
            }
        }
    }
}
