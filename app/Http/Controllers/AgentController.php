<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    public function create_agent(Request $request) {
        $request->validate([
            'name' => "required|string",
            'email' => "required|email|unique:agents",
            'password' => "required|string",
        ]);
        $newAgent = Agent::create([
            'name' => $request->name,
            'agent_id' => "AGENT".rand(100000,999999),
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return response()->json([
            "data" => $newAgent,
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
        $agent = Auth::user();
        return response()->json([
            "agent" => $agent,
            "satus" => "success",
            "message" => "Successful."
        ], 200);
    }


    public function login(Request $request) {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string"
        ]);
        $agent = Agent::where('email', $request->email)->first();

        if (!$agent || !Hash::check($request->password, $agent->password)) {
            return response([
                "message" => "The provided credentials are incorrect.",
                "status" => "error"
            ], 400);
        }
        return response([
            'agent' => $agent,
            "status" => "success",
            "message" => "Login Successful.", 
            'token' => $agent->createToken('webapp', ['role:agent'])->plainTextToken
        ]);
    }
}
