<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'set_id',
        'name',
        'order'
    ];

    public function set() {
        return $this->belongsTo(Set::class, 'set_id');
    }  

    public function CompletedLesson() {
        return $this->hasMany(CompletedLesson::class, 'lesson_id');
    }

    public function contents() {
        return $this->hasMany(LessonContent::class, 'lesson_id');
    }
}
