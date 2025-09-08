<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\Subject;
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

        // Create Classes for each grade level
        foreach ($gradeLevels as $grade) {
            ClassModel::firstOrCreate([
                'grade_level' => $grade,
                'name' => "Section A",
                'teacher_id' => null, // assign as needed
            ]);
        }

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

            $student = Student::create([
                'first_name' => "Student",
                'last_name' => "$i",
                'class_id' => $class->id,
                'parent_id' => $parent->id,
                'approved_by_teacher' => true
            ]);

            // Fetch subjects for this student's grade level
            $subjects = Subject::where('grade_level', $class->grade_level)->get();

            foreach ($subjects as $subject) {
                for ($quarter = 1; $quarter <= 4; $quarter++) {
                    Grade::create([
                        'student_id' => $student->id,
                        'class_id'   => $class->id,
                        'subject_id' => $subject->id,
                        'quarter'    => $quarter,
                        'grade'      => rand(75, 99),
                    ]);
                }
            }
        }
    }
}
