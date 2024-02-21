<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ApiAdminSignupController extends Controller
{
    public function index(Request $request) {
        if ($request->is('api/*')) {
            $users = User::get();
            return response()->json(['users' => $users]);
        }
    }
    
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            Log::info('Before creating user record');
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
            Log::info('After creating user record');
        } catch (\Exception $e) {
            Log::error('Error creating user record: ' . $e->getMessage());
            return response()->json(["error" => "Failed to create user"], 500);
        }

        return response()->json(["message" => "Successfully created the user's data"]);
    }
}
