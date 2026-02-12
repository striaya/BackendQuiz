<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_published'
    ];

    public function sets(){
        return $this->hasMany(Set::class, 'course_id');
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class, 'course_id');
    }

}
