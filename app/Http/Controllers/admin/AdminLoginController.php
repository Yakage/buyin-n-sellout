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

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {

            if (Auth::attempt(['email' => $request->email,'password' => 
            $request->password], $request->get('remember'))) {

                $admin = Auth::user();

                if ($admin->role == '1') {
                    return redirect()->route('admin.dashboard');
                } else {

                    Auth::logout();
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
