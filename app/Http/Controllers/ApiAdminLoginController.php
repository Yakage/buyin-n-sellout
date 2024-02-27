<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiAdminLoginController extends Controller
{
    public function authenticate(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            $admin = Auth::guard('admin')->user();

            if ($admin->role == 1) {
                // Generate and return token if needed
                $token = $admin->createToken('Admin Access Token')->plainTextToken;

                return response()->json(['token' => $token, 'admin' => $admin]);
            } else {
                Auth::guard('admin')->logout();
                return response()->json(['error' => 'You are not authorized to access admin panel.'], 401);
            }
        } else {
            return response()->json(['error' => 'Either Email/Password is incorrect'], 401);
        }
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Logout successful']);
    }
}
