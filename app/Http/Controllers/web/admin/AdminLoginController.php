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

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if ($validator->passes()) {
            if (auth()->guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                $admin = auth()->guard('admin')->user();
    
                if ($admin->role == 2) {
                    return redirect()->route('admin.dashboard');
                    // return response()->json([
                    //     'status' => true,
                    //     'message' => 'Login successful',
                    //     'redirect' => route('admin.dashboard'),
                    // ]);
                } else {
                    auth()->guard('admin')->logout();
                    return response()->json([
                        'status' => false,
                        'message' => 'You are not authorized to access the admin panel.',
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Either Email/Password is incorrect',
                ], 401);
            }
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    }

    // public function login(Request $request) {

    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     if ($validator->passes()) {

    //         if (Auth::guard('admin')->attempt(['email' => $request->email,'password' => 
    //         $request->password], $request->get('remember'))) {

    //             $admin = Auth::guard('admin')->user();

    //             if ($admin->role == 2) {
    //                 return redirect()->route('admin.dashboard');
    //             } else {

    //                 Auth::guard('admin')->logout();
    //                 return redirect()->route('admin.login')->with('error', 'You are not authorized to access
    //                 admin panel.');
    //             }

    //         } else {
    //             return redirect()->route('admin.login')->with('error', 'Either Email/Password is 
    //             incorrect');

    //         }
    //     }else {
    //         return redirect()->route('admin.login')
    //         ->withErrors($validator)
    //         ->withInput($request->only('email'));
    //     }
    // }

    public function logout() {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
