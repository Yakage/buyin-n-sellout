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

        if ($validator->passes()) {
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                $admin = Auth::guard('admin')->user();
                
                if ($admin->role == 1) {
                    return response()->json(['message' => 'Login successful', 'redirect' => route('admin.dashboard')]);
                } else {
                    Auth::guard('admin')->logout();
                    return response()->json(['error' => 'You are not authorized to access admin panel.']);
                }
            } else {
                return response()->json(['error' => 'Either Email/Password is incorrect']);
            }
        } else {
            return response()->json(['error' => $validator->errors()->first()]);
        }
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Logout successful']);
    }
}
