<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

        if ($validator->passes()) {
            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                $admin = Auth::guard('admin')->user();

                if($admin->role == 1) {
                    return redirect()->route('admin.dashboard');
                } else {

                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not authorized to access admin panel.');
                }
            } else {
                return redirect()->route('admin.login')->with('error, "Either email/password is incorrect.');
            }
        } else {
            return redirect()->route('admin.login')->withErrors($validator)->withInput($request->only('email'));
        }

        //     if (Auth::guard('admin')->attempt(['email' => $request->email,'password' => $request-> password,'role'=>$request->role], $request->get('remember'))) {

        //         $admin = Auth::guard('admin')->user();

        //         if ($admin->role == 1) {
        //             return redirect()->route('admin.dashboard');
        //         } else {
        //             Auth::guard('admin')->logout();
        //             return redirect()->route('admin.login')->with('error', 'You are not authorized to access
        //             admin panel.');
        //         }

        //     } else {
        //         return redirect()->route('admin.login')->with('error', 'Either Email/Password is 
        //         incorrect');

        //     }
        // } else {
        //     return redirect()->route('admin.dashboard')
        //     ->withErrors($validator)
        //     ->withInput($request->only('email'));
        // }
    }

    // public function authenticate(Request $request) {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->route('admin.register')
    //             ->withErrors($validator)
    //             ->withInput($request->only('email'));
    //     }

    //     if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
    //         $admin = Auth::guard('admin')->user();

    //         if ($admin->role == 1) {
    //             return redirect()->route('admin.dashboard');
    //         } else {
    //             Auth::guard('admin')->logout();
    //             dd('Unauthorized access');
    //             return redirect()->route('admin.login')->with('error', 'You are not authorized to access admin panel.');
    //         }
    //     } else {
    //         $errorMessage = 'Either Email/Password is incorrect';
    //         return redirect()->route('admin.login')->with('error', $errorMessage);
    //     }
    // }


    public function logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}

