<?php

namespace App\Http\Controllers;

use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SetController extends Controller
{
    //Set Add
    public function setadd(Request $request, $course_id) {
        //Validasi
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "order" => "required|integer"
        ]);

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
                "status" => "insullfficient_permissions",
                "message" => "Acces forbidden"
            ], 403);
        }

        //Response Invalid Field
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255"
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid field(s) in request",
                "errors" => $validator->errors(),
            ], 400);
        }

        //Response Success
        $set = Set::create([
            "name" => $validated['name'],
            "order" => $validated['order'],
            "course_id" => $course_id,
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Set successfully added",
            "data" => [
                "name" => $set->name,
                "order" => $set->order,
                "id" => $set->id
            ]
        ], 201);
    }

    //Set Delete
    public function setdelete(Request $request, $course_id, $set_id){

        $validator = Validator::make($request->all(), [
        "name" => "required|string|"
    ]);

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
            "message" => "Acces forbidden"
        ], 403);
    }

    //Response Invalid Field
    $deleted = Set::where('id', $set_id)
    ->where('course_id', $course_id)
    ->delete();

    if(!$deleted) {
        return response()->json([
            "status" => "error",
            "message" => "Invalid field(s) in request",
            "errors" => [
                $validator
            ]
        ], 400);
    }

    //Response Success
    return response()->json([
        "status" => "success",
        "message" => "Set successfully deleted"
    ], 200);
    }
}