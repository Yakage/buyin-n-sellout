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
            'password' => 'required',
        ]);

        if ($validator->passes()) {
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                $admin = Auth::guard('admin')->user();

                if ($admin->role == 1) {
                    $token = $admin->createToken('admin-token')->plainTextToken;

                    return response()->json(['message' => 'Login successful', 'admin' => $admin, 'token' => $token], 200);
                } else {
                    Auth::guard('admin')->logout();
                    return response()->json(['error' => 'You are not authorized to access the admin panel.'], 403);
                }
            } else {
                return response()->json(['error' => 'Either email or password is incorrect.'], 401);
            }
        } else {
            return response()->json(['error' => $validator->errors()], 422);
        }
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Logout successful']);
    }
}
