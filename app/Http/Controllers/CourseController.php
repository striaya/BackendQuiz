<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    //Add Course
    public function courses(Request $request)
{
    // Response Invalid Token
    $user = $request->user();
    if (!$user) {
        return response()->json([
            "status" => "invalid_token",
            "message" => "Invalid or expired token"
        ], 401);
    }

    // Response Forbidden
    if ($user->role !== 'admin') {
        return response()->json([
            "status" => "insufficient_permissions",
            "message" => "Access forbidden"
        ], 403);
    }

    // Validasi
    $validated = $request->validate([
        "name" => "required|string|max:255",
        "description" => "nullable|string",
        "slug" => "required|string|unique:courses,slug"
    ]);

    // Response Success
    $course = Course::create([
        "name" => $validated['name'],
        "description" => $validated['description'],
        "slug" => Str::slug($validated['slug']),
    ]);

    return response()->json([
        "status" => "success",
        "message" => "Course successfully added",
        "data" => $course
    ], 201);

    //Response Invalid Field
    $validator = Validator::make($request->all(), [
    "name" => "required|string|max:255",
    "description" => "nullable|string",
    "slug" => "required|string|unique:courses,slug"
]);

if ($validator->fails()) {
    return response()->json([
        "status" => "error",
        "message" => "Invalid field(s) in request",
        "errors" => $validator->errors(),
    ], 400);
}
}

    //Course Update
    public function courseedit(Request $request, $course_slug) {

    //Response Invalid Token
        $validasi = $request->user();

        if( !$validasi ) {
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token"
            ], 401);
        }

    //Response Forbidden
        if ($validasi->role !== 'admin') {
            return response()->json([
                "status" => "insufficient_permissions",
                "message" => "Access forbidden"
            ], 403);
        }

    //Response Not Found
        $course = Course::where('slug', $course_slug)->first();

        if(!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "description" => "nullable|string",
            "is_published" => "nullable|boolean"
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid field(s) in request",
                "errors" => $validator->errors(),
            ], 400);
        }

        $course->update([
            "name" => $request->name,
            "description" => $request->description,
            "is_published" => $request->is_published ?? $course->is_published,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Course successfully updated",
            "data" => [
                "id" => $course->id,
                "name" => $course->name,
                "slug" => $course->slug,
                "description" => $course->description,
                "is_published" => $course->is_published,
                "created_at" => $course->created_at,
                "updated_at" => $course->updated_at
            ]
        ], 200);
    }

    //Course Deleted
    public function coursedelete(Request $request, $course_slug) {

    //Response Invalid Token    
        $user = $request->user();
        if(!$user) {
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token"
            ], 401);
        }

    //Response Forbidden
        if($user->role !== 'admin') {
            return response()->json([
                "status" => "insufficient_permissions",
                "message" => "Access forbidden"
            ], 403);
        }

        $course = Course::where('slug', $course_slug)->first();

    //Response Not Found
        if(!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $course->delete();

    //Response Success
        return response()->json([
            "status" => "success",
            "message" => "Course successfully deleted"
        ], 200);
    }

    //Get All Published Course
    public function coursegetall(Request $request){

    //Response Invalid Token
    $user = $request->user();
    if(!$user) {
        return response()->json([
            "status" => "invalid_token",
            "message" => "Invalid or expired"
        ], 401);
    }

    $course = Course::where('is_published', true)->get();

    //Response Success
    return response()->json([
        "status" => "success",
        "message" => "Courses retrieved successfully",
        "data" => [
           "courses" => $course
        ]
    ], 200);
    }

    public function coursedetail(Request $request, $course_slug) {

        //Response Invalid Token
        $user = $request->user();
        if(!$user) {
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token"
            ], 401);
        }

        //Response Success
        $course = Course::with([
            'sets.lessons.contents.options'
        ])
        ->where('slug', $course_slug)
        ->where('is_published', true)
        ->first();

        if(!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "message" => "Course details retrieved successfully",
            "data" => $course
        ], 200);
    }

    //Resgiter to a Course
    public function courseregister(Request $request, $course_slug){

    //Response Invalid Token
        $user = $request->user();
        if(!$user) {
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token"
            ], 401);
        }
    //Response Not Found
        $course = Course::where('slug', $course_slug)->first();
        if(!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        //Response Already Registered
        $already = DB::table('course_user')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($already) {
            return response()->json([
                "status" => "error",
                "message" => "The user is already registered for this course"
            ], 400);

            //Response Success
            DB::table('course_user')->insert([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                "status" => "success",
                "message" => "User registered successful"
            ], 201);
        }
    }
}