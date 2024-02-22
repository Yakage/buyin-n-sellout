<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
}
