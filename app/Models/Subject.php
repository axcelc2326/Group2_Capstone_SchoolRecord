<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name'];

    // A subject can have many grades
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
