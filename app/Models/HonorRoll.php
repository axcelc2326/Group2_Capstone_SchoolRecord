<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HonorRoll extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'school_year',
        'quarter',
        'principal_name',
        'with_honors_count',
        'with_high_honors_count',
        'with_highest_honors_count',
        'achievers_count',
        'pdf_path',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
