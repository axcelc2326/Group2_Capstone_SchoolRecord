<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            'English',
            'Filipino',
            'Mathematics',
            'Science',
            'Araling Panlipunan',
            'Edukasyon sa Pagpapakatao',
            'MAPEH',
            'EPP/TLE',
        ];

        // Example: For Grade 1 to Grade 6 (elementary)
        for ($grade = 1; $grade <= 6; $grade++) {
            foreach ($subjects as $subject) {
                Subject::firstOrCreate([
                    'name' => $subject,
                    'grade_level' => $grade,
                ]);
            }
        }
    }
}
