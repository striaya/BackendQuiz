<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $fillable = [
        'name',
        'course_id',
        'order'
    ];

    public function lessons() {
        return $this->hasMany(Lesson::class, 'set_id');
    }

    public function Course() {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
