<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = ['title', 'body', 'class_id', 'created_by', 'expires_at'];

    // ✅ Reference to the user who created the announcement
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    
    // ✅ Add this scope to easily get active announcements
    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }
    
    // ✅ Optional: Add a scope for expired announcements
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}