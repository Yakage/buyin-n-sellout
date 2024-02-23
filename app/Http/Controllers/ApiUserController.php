<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::latest();

        if (!empty($request->get('keyword'))) {
            $keyword = $request->get('keyword');
            $users = $users->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }
        
        $users = $users->paginate(10);

        return response()->json([
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|min:5',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->status = $request->status;
        $user->password = Hash::make($request->password);
        $user->save();

        $message = 'User added successfully';

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id . ',id',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->status = $request->status;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $message = 'User updated successfully.';

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->delete();

        $message = 'User deleted successfully.';

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
}
