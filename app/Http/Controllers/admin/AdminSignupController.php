<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AdminSignupController extends Controller
{
    public function index(Request $request)
    {
        $users = User::get();

        return view('admin.index', ['users' => $users]);
    }

    public function store(Request $request)
    {
        // If the request is a GET request, render the registration form
        if ($request->isMethod('get')) {
            return view('admin.register');
        }

        // If the request is a POST request, handle form submission
        $validator = Validator::make($request->all(), [
            'name' => 'required|name',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $requestData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ];

        try {
            Log::info('Before creating user record');
            $user = User::create($requestData);
            Log::info('After creating user record');
        } catch (\Exception $e) {
            Log::error('Error creating user record: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create user record.');
        }

        return redirect()->route('admin.index')->with('success', "Successfully created the user's data");
    }

    public function authenticate(Request $request) {

        if ($request->is('api/*')) {
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
                    // Generate and return token if needed
                    $token = $admin->createToken('Admin Access Token')->plainTextToken;
    
                    return response()->json(['token' => $token, 'admin' => $admin]);
                } else {
                    Auth::guard('admin')->logout();
                    return response()->json(['error' => 'You are not authorized to access admin panel.'], 401);
                }
            } else {
                return response()->json(['error' => 'Either Email/Password is incorrect'], 401);
            }
            
        }else{
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
        
    }
}
