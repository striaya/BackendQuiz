<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletedLesson extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id'
    ];

    public function User() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Lesson() {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}
