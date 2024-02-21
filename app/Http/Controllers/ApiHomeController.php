<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiHomeController extends Controller
{
    public function index(){
        // Since this is an API controller, you might want to return JSON response instead of rendering a view
        // If you need to authenticate the user before accessing the dashboard, you can uncomment the code below
        // $admin = Auth::guard('admin')->user();
        // return response()->json(['message' => 'Welcome ' . $admin->name]);
        
        // For simplicity, I'm just returning a success message
        return response()->json(['message' => 'Welcome to the admin dashboard']);
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
