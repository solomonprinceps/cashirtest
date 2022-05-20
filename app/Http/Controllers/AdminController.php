<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function create_admin(Request $request) {
        $request->validate([
            'name' => "required|string",
            'email' => "required|email|unique:admins",
            'password' => "required|string",
        ]);
        $newAdmin = Admin::create([
            'name' => $request->name,
            'admin_id' => "ADMIN".rand(100000,999999),
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return response()->json([
            "data" => $newAdmin,
            "satus" => "success",
            "message" => "Admin created successfully."
        ], 200);
    }

    public function logout() {
        Auth::user()->tokens()->delete();
        return response([
            "status" => "success",
            "message" => "Admin logout successfully",
        ], 200);
    }

    public function getProfile() {
        $admin = Auth::user();
        return response()->json([
            "admin" => $admin,
            "satus" => "success",
            "message" => "Successful."
        ], 200);
    }

    public function login(Request $request) {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string"
        ]);
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response([
                "message" => "The provided credentials are incorrect.",
                "status" => "error"
            ], 400);
        }
        return response([
            'admin' => $admin,
            "status" => "success",
            "message" => "Login Successful.", 
            'token' => $admin->createToken('webapp', ['role:admin'])->plainTextToken
        ]);
    }
}
