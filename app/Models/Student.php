<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',   // ✅ Added Middle Name
        'last_name',
        'lrn',           // ✅ Added LRN
        'gender',        // ✅ Added Gender
        'class_id',
        'parent_id',
        'approved_by_teacher',
    ];

    /**
     * A student belongs to a class.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * A student belongs to a parent (user).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * A student has many grades.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * A student has many grade remarks (promoted/retained).
     */
    public function gradeRemarks(): HasMany
    {
        return $this->hasMany(GradeRemark::class);
    }

    /**
     * (Optional) If you want to directly link student ↔ user account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
