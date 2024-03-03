<?php

namespace App\Http\Controllers\web\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function index() {
        return view('admin.login');
    }

    // public function login(Request $request){
    //     $credentials = $request->only('email', 'password');
        
    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();

    //         // Check if the user is an admin
    //         if ($user->role == 2) {
    //             return redirect()->route('admin.dashboard'); // Redirect to admin dashboard
    //         }

    //         return redirect()->route('front.home'); // Redirect to user dashboard
    //     }

    //     return redirect()->route('admin.login')->with("error", "Invalid Credentials");
    // }

    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {

            if (Auth::guard('admin')->attempt(['email' => $request->email,'password' => 
            $request->password], $request->get('remember'))) {

                $admin = Auth::guard('admin')->user();

                if ($admin->role == 2) {
                    return redirect()->route('admin.dashboard');
                } else {

                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not authorized to access
                    admin panel.');
                }

            } else {
                return redirect()->route('admin.login')->with('error', 'Either Email/Password is 
                incorrect');

            }
        }else {
            return redirect()->route('admin.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));
        }
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
