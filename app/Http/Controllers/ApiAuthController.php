<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public function login(Request $request) {
        return response()->json(['message' => 'Please use your own frontend for login.']);
    }

    public function register(Request $request) {
        return response()->json(['message' => 'Please use your own frontend for registration.']);
    }

    public function processRegister(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'
        ]);

        if($validator->passes()) {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save(); 

            return response()->json(['status' => true, 'message' => 'You have been registered successfully.']);
        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }
    }

    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->passes()) {
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                return response()->json(['status' => true, 'message' => 'User authenticated successfully.']);
            } else {
                return response()->json(['status' => false, 'message' => 'Either email/password is incorrect.'], 401);
            }
        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }
    }

    public function profile() {
        // Assuming this method returns user profile information
        $user = Auth::user();
        return response()->json(['user' => $user]);
    }

    public function logout() {
        Auth::logout();
        return response()->json(['message' => 'User logged out successfully.']);
    }

    public function orders() {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();
        return response()->json(['orders' => $orders]);
    }

    public function orderDetail($id) {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->where('id', $id)->first();
        $orderItems = OrderItem::where('order_id', $id)->get();
        return response()->json(['order' => $order, 'orderItems' => $orderItems]);
    }

    public function wishlist() {
        $wishlists = Wishlist::where('user_id', Auth::user()->id)->with('product')->get();
        return response()->json(['wishlists' => $wishlists]);
    }

    public function removeProductFromWishList(Request $request) {
        $wishlist = Wishlist::where('user_id', Auth::user()->id)->where('product_id', $request->id)->first();
        if($wishlist == null) {
            return response()->json(['status' => true, 'message' => 'Product already removed.']);
        } else {
            Wishlist::where('user_id', Auth::user()->id)->where('product_id', $request->id)->delete();
            return response()->json(['status' => true, 'message' => 'Product removed successfully.']);
        }
    }
}
