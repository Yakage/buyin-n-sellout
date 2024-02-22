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
    public function index(Request $request) {
        if ($request->is('api/*')) {
            $users = User::get();

            return response()->json(['users' => $users]);
        }
    }
    

    public function store(Request $request) {
        if ($request->is('api/*')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|name',
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $requestData = 
            [
                'name' => $request -> name,
                'email' => $request -> email,
                'password' => $request -> password
            ];
        }

        try {
            Log::info('Before creating user record');
            $users = User::create($requestData);
            Log::info('After creating user record');
        } catch (\Exception $e) {
            Log::error('Error creating user record: ' . $e -> getMessage());
        }

        return response()->json(["message" => "Successfully created the user's data"]);
    }
}
