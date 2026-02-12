<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id'
    ];

    public function User(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Course() {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
