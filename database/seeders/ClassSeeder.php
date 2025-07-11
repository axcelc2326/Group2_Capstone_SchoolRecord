<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel; // Replace with your actual model name

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $gradeLevels = [1, 2, 3, 4, 5, 6];

        foreach ($gradeLevels as $level) {
            ClassModel::create([
                'name' => 'Grade ' . $level,
                'grade_level' => $level,
                'teacher_id' => null, // Optional: Assign later
            ]);
        }
    }
}
