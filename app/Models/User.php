<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, Notifiable;
    
    protected $fillable = [
        'full_name',
        'username',
        'password',
        'role',
        'remember_token'
    ];

    public function Enrollment() {
        return $this->hasOne(Enrollment::class, 'user_id');
    }

    public function CompletedLesson() {
        return $this->belongsTo(CompletedLesson::class, 'user_id');
    }
}
