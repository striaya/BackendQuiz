<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

//Register
    public function register(Request $request) {
        $request->validate([
            'full_name' => 'required',
            'username' => 'required|min:3|unique:users,username|regex:/^[a-zA-Z0-9._]+$/',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'remember_token' => null
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                "status" => "success",
                "message" => "User registration successfull",
                "data" => [
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'updated_at' => $user->updated_at,
                    'created_at' => $user->created_at,
                    'id' => $user->id,
                    'token' => $token,
                    'role' => $user->role
                ]
            ], 201);

            $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'username' => 'required|min:3|unique:users,username|regex:/^[a-zA-Z0-9._]+$/',
            'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid field(s) in request',
                    'errors' => $request->errors(),
                ], 400);
            }
        }

        //Login
        public function login(Request $request) {
            $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);

            $user = user::where('username', $request->username)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    "status" => "authentication_failed",
                    "message" => "The username or password you entered is incoreect"
                ], 400);
            };

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                "status" => "success",
                "message" => "Login Successful",
                "data" => [
                    "id" => $user->id,
                    "username" => $user->username,
                    "created_at" => $user->created_at,
                    "updated_at" => $user->updated_at,
                    "token" => $token,
                    "role" => $user->role
                ]
            ], 200);
        }

        //Logout
        public function logout(Request $request) {
            $request->user()->tokens()->delete();

            return response()->json([
                "status" => "success",
                "message" => "Logout successful"
            ], 200);

            $user = $request->user();

            if( !$user) {
                return response()->json([
                    "status" => "invalid_token",
                    "message" => "Invalid or expired token"
                ], 401);
            }
        }
    } 
