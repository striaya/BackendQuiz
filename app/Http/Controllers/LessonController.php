<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    //Add Lesson
    public function lessonadd(Request $request){
        //Validasi
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "set_id" => "required|exists:sets,id",
            "contents" => "required|array",
            
            "contents.*.type" => ['required', 'in:learn,quiz'],
            "contents.*.content" => ['required', 'string'],

            "contents.*.options" => ['required_if:contents.*.type,quiz', 'array'],
            "contents.*.options.*.option_text" => ['required', 'string'],
            "contents.*.options.*.is_correct" => ['required', 'boolean'],
        ]);

        //Response Invalid Token
        $user = $request->user();
        if(!$user) {
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token",
            ], 401);
        }

        //Response Forbidden
        if ($user->role !== 'admin') {
            return response()->json([
                "status" => "insufficient_permissions",
                "message" => "Access forbidden",
            ], 403);
        }

        //Response Invalid Field
        if($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid field(s) in request",
                "errors" => $validator->errors()
            ], 400);
        }
 
        //Response Success
        $validated = $validator->validated();
        
        $lesson = Lesson::create([
            "name" => $validated['name'],
            "order" => $request->order,
            "set_id" => $validated['set_id'],
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Lesson successfully added",
            "data" => $lesson
        ], 201);
    }

    //Delete Lesson
    public function lessondelete(Request $request, $lesson_id){
        //Response Invalid Token
        $user = $request->user();
        if(!$user) {
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token",
            ], 401);
        }

        //Response Forbidden
        if($user->role !== 'admin') {
            return response()->json([
                "status" => "insufficient_permissioons",
                "message" => "Access forbidden"
            ], 403);
        }

        //Response Not Found
        $lesson = Lesson::find($lesson_id);

        if(!$lesson) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $lesson->delete();
        
        //Response Success
        return response()->json([
            "status" => "success",
            "message" => "Lesson successfully deleted"
        ], 200);
    }

    //Check Answer User
    public function lessondetail(Request $request, $lesson_id, $content_id){

    //Validasi
    $request->validate([
        'option_id' => "required|exists:options,id"
    ]);

    $option = Option::where('id', $request->option_id)
                ->where('lesson_content_id', $content_id)
                ->first();

        if(!$option) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        //Response Invalid Token
        $user = $request->user();
        if(!$user){
            return response()->json([
                "status" => "invalid_token",
                "message" => "Invalid or expired token"
            ], 401);
        }

        //Response Forbidden
        if ($user->role !== 'user') {
            return response()->json([
                "status" => "insufficient_permissions",
                "message" => "Access forbidden"
            ], 403);
        }

        //Response Not Found
        $lesson = Lesson::find($lesson_id);
        if(!$lesson) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $content = LessonContent::where('id', $content_id)
                    ->where('lesson_id', $lesson_id)
                    ->first();

        if(!$content) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        //Response Onlt For Quiz Type
        if($content->type !== 'quiz') {
            return response()->json([
                "status" => "error",
                "message" => "Only for quiz content"
            ], 400);
        }

        return response()->json([
            "status" => "success",
            "message" => "Check answer success",
            "data" => [
                "question" => $content->content,
                "user_answer" => $option->option_text,
                "is_correct" => (bool) $option->is_correct
            ]
        ], 200);
    }

    //Complete Lesson
public function lessonedit(Request $request, $lesson_id)
{
    // Invalid Token
    $user = $request->user();
    if (!$user) {
        return response()->json([
            "status" => "invalid_token",
            "message" => "Invalid or expired token"
        ], 401);
    }

    // Forbidden
    if ($user->role !== 'user') {
        return response()->json([
            "status" => "insufficient_permissions",
            "message" => "Access forbidden"
        ], 403);
    }

    // Not Found
    $lesson = Lesson::find($lesson_id);
    if (!$lesson) {
        return response()->json([
            "status" => "not_found",
            "message" => "Resource not found"
        ], 404);
    }

    // SUCCESS RESPONSE
    return response()->json([
        "status" => "success",
        "message" => "Lesson successfully completed"
    ], 200);
}

}
