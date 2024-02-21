<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'
        ]);

        if ($validator->passes()) {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save(); 

            return response()->json([
                'status' => true,
                'message' => 'User registered successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                return response()->json([
                    'status' => true,
                    'message' => 'User authenticated successfully.'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Either email/password is incorrect.'
                ], 401);
            }
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    }

    public function logout() {
        Auth::logout();
        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully.'
        ]);
    }
}
