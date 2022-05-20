<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function create_user(Request $request) {
        $request->validate([
            'name' => "required|string",
            'email' => "required|email|unique:users",
            'password' => "required|string",
        ]);
        $newAdmin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return response()->json([
            "data" => $newAdmin,
            "satus" => "success",
            "message" => "Admin created successfully."
        ], 200);
    }

    public function getProfile() {
        $user = Auth::user();
        return response()->json([
            "user" => $user,
            "satus" => "success",
            "message" => "Successful."
        ], 200);
    }

    public function logout() {
        Auth::user()->tokens()->delete();
        return response([
            "status" => "success",
            "message" => "User logout successfully",
        ], 200);
    }

    public function login(Request $request) {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string"
        ]);
        $users = User::where('email', $request->email)->first();

        if (!$users || !Hash::check($request->password, $users->password)) {
            return response([
                "message" => "The provided credentials are incorrect.",
                "status" => "error"
            ], 400);
        }
        return response([
            'users' => $users,
            "status" => "success",
            "message" => "Login Successful.", 
            'token' => $users->createToken('webapp', ['role:user'])->plainTextToken
        ]);
    }
}
