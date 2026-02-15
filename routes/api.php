<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SetController;
use App\Http\Controllers\LessonController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/users/progress', [UserController::class, 'userprogress']);
    Route::post('/logout', [UserController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/courses', [CourseController::class, 'coursegetall']);
    Route::post('/courses', [CourseController::class, 'courses']);
    Route::post('/courses/{course_slug}/register', [CourseController::class, 'courseregister']);
    Route::get('/courses/{course_slug}', [CourseController::class, 'coursedetail']);
    Route::put('/courses/{course_slug}', [CourseController::class, 'courseedit']);
    Route::delete('/courses/{course_slug}', [CourseController::class, 'coursedelete']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/courses/{course}/sets', [SetController::class, 'setadd']);
    Route::delete('/courses/{course}/sets/{set_id}', [SetController::class, 'setdelete']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/lessons', [LessonController::class, 'lessonadd']);
    Route::delete('/lessons/{lesson_id}', [LessonController::class, 'lessondelete']);
    Route::post('/lessons/{lesson_id}/contents/{content_id}/check', [LessonController::class, 'lessondetail']);
    Route::put('/lessons/{lesson_id}/complete', [LessonController::class, 'lessonedit']);
});