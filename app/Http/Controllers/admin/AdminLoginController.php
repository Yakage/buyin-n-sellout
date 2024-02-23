<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
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
            // Handle validation failure for both API and non-API scenarios
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            $admin = Auth::guard('admin')->user();

            if ($admin->role == 2) {
                // Check if it's an API request
                if ($request->is('api/admin/login')) {
                    $token = $admin->createToken('Admin Access Token')->plainTextToken;
                    return response()->json(['token' => $token, 'admin' => $admin]);
                } else {
                    return redirect()->route('admin.dashboard');
                }
            } else {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('error', 'You are not authorized to access admin panel.');
            }
        } else {
            // Handle authentication failure for both API and non-API scenarios
            $errorMessage = 'Either Email/Password is incorrect';
            if ($request->is('api/admin/login')) {
                return response()->json(['error' => $errorMessage], 401);
            } else {
                return redirect()->route('admin.login')->with('error', $errorMessage);
            }
        }
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
