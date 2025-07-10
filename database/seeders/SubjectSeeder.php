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

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['name' => $subject]);
        }
    }
}
