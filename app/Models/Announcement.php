<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    // ✅ Specify which fields can be mass-assigned
    protected $fillable = ['title', 'body', 'created_by'];


    /**
     * ✅ Each announcement is created by one user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
