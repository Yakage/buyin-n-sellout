<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiSettingController extends Controller
{
    public function showChangePasswordForm()
    {
        return response()->json([
            'message' => 'Change password form'
        ]);
    }

    public function processChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password'
        ]);

        $id = Auth::guard('admin')->user()->id;

        $admin = User::where('id', $id)->first();

        if ($validator->passes()) {
            if (!Hash::check($request->old_password, $admin->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your Old Password is incorrect, please try again.'
                ]);
            }

            User::where('id', $id)->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'You have successfully changed your password.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
