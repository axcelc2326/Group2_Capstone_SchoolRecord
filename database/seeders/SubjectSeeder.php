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

        // Grade levels as strings (K1, K2, 1-6)
        $gradeLevels = ['K1', 'K2', '1', '2', '3', '4', '5', '6'];

        foreach ($gradeLevels as $grade) {
            foreach ($subjects as $subject) {
                Subject::firstOrCreate([
                    'name' => $subject,
                    'grade_level' => $grade,
                ]);
            }
        }
    }
}
