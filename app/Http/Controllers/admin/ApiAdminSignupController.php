<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ApiAdminSignupController extends Controller
{
    public function index()
    {
        // You can decide whether to return HTML view or JSON response for the index method based on the request type
        return view('admin.register');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            $admin = Auth::guard('admin')->user();

            if ($admin->role == 2) {
                return response()->json(['message' => 'Authentication successful', 'redirect' => route('admin.dashboard')]);
            } else {
                Auth::guard('admin')->logout();
                return response()->json(['error' => 'You are not authorized to access admin panel.'], 403);
            }
        } else {
            $errorMessage = 'Either Email/Password is incorrect';
            return response()->json(['error' => $errorMessage], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6',
            // Add more validation rules as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $admin = new User();
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);
        // Add more fields as needed for admin registration

        $admin->save();

        return response()->json(['message' => 'Admin registered successfully. You can now log in.']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Logout successful']);
    }
}
