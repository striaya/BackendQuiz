<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function lessonadd(Request $request){
        //Validasi
        $validated = $request->validate([
            "name" => "required",
            "set_id" => "required|exists:sets:id",
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
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "contents" => "required|array"
        ]);
    }
}
