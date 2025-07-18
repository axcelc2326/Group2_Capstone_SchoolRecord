<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Grade;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ParentsWithChildrenSeeder extends Seeder
{
    public function run(): void
    {
        // Create Parent Role if not exists
        Role::firstOrCreate(['name' => 'parent']);

        // Create Classes (Grade 1-6)
        for ($grade = 1; $grade <= 6; $grade++) {
            ClassModel::firstOrCreate([
                'grade_level' => $grade,
                'name' => "Section A",
                'teacher_id' => $grade + 1
            ]);
        }

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

            // Create random grades for 8 subjects (subject_id 1-8) across 4 quarters
            for ($subjectId = 1; $subjectId <= 8; $subjectId++) {
                for ($quarter = 1; $quarter <= 4; $quarter++) {
                    Grade::create([
                        'student_id' => $student->id,
                        'class_id' => $class->id,
                        'subject_id' => $subjectId,
                        'quarter' => $quarter,
                        'grade' => rand(75, 99),
                    ]);
                }
            }
        }
    }
}
