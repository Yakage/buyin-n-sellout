<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiAdminLoginController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Welcome to the admin login.'], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user is an admin
            if ($user->role == 2) {
                return response()->json(['message' => 'Login successful', 'redirect' => route('admin.dashboard')], 200);
            }

            return response()->json(['message' => 'Login successful', 'redirect' => route('front.home')], 200);
        }

        return response()->json(['error' => 'Invalid Credentials'], 401);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }
}
