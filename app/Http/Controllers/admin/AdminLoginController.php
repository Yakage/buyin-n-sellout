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

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user is an admin
            if ($user->role === 1) {
                return redirect()->route('admin.dashboard'); // Redirect to admin dashboard
            }
             // Check if not already active and update
                
            return redirect()->route('front.home'); // Redirect to user dashboard
        }

        return redirect()->route('admin.login')->with("error", "Invalid Credentials");
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
