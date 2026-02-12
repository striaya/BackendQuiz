<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'lesson_content_id',
        'option_text',
        'is_correct'
    ];

    public function content() {
        return $this->belongsTo(LessonContent::class, 'lesson_content_id');
    }
}
