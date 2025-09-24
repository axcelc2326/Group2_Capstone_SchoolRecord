<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeRemark extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'final_average',
        'remarks',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }
}
