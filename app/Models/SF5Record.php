<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SF5Record extends Model
{
    use HasFactory;

    // ✅ Explicitly define the correct table name
    protected $table = 'sf5_records';

    protected $fillable = [
        // 🏫 School Information
        'region',
        'division',
        'school_id',
        'school_name',
        'school_year',
        'school_head_chair',

        // 🧑‍🏫 Relationships
        'class_id',
        'teacher_id',

        // 👨‍🎓 Student Count Summary
        'male_count',
        'female_count',
        'overall_count',
        'class_average',

        // 🏅 Action Taken Summary
        'promoted_male',
        'promoted_female',
        'conditional_male',
        'conditional_female',
        'retained_male',
        'retained_female',

        // 📊 Level of Progress and Achievement
        'below_75_male',
        'below_75_female',
        'fair_75_79_male',
        'fair_75_79_female',
        'satisfactory_80_84_male',
        'satisfactory_80_84_female',
        'very_satisfactory_85_89_male',
        'very_satisfactory_85_89_female',
        'outstanding_90_100_male',
        'outstanding_90_100_female',
    ];

    // ✅ Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
