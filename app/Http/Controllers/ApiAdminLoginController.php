<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAdminLoginController extends Controller
{
    public function index() {
        return view('admin.login');
    }

    public function authenticate(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->error()], 422);
        }

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            $admin = Auth::guard('admin')->user();

            if ($admin->role == 2) {
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
        return response()->json(['message' => 'Logged out successfully']);
    }
}
