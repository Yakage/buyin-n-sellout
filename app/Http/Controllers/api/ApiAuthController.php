<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordEmail;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    public function register()
    {
        return response()->json([
            'status' => true,
            'message' => 'Welcome to the registration endpoint!'
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return response()->json([
                'status' => true,
                'message' => 'Login successful.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        $countries = Country::orderBy('name', 'ASC')->get();
        $address = CustomerAddress::where('user_id', $user->id)->first();

        return response()->json([
            'user' => $user,
            'countries' => $countries,
            'address' => $address,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $userId = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $userId . ',id',
            'phone' => 'required',
        ]);

        if ($validator->passes()) {
            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    }

    public function updateAddress(Request $request)
    {
        $userId = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country_id' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->passes()) {
            CustomerAddress::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_id' => $userId,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country_id,
                    'address' => $request->address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip,
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Address Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    }

    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed',
            'password_confirmation' => 'required|string|same:password'
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
                'message' => 'Registration successful. Redirecting to login page',
                'redirect' => route('account.login')
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->passes()) {
            if (Auth::attempt($validator)) {
                $tokenResult = $user->createToken('Personal Access Token');
                $accessToken = $tokenResult->plainTextToken;
                $user->update(['api_token' => $accessToken]);
                $user = $request->user();
                return response()->json(['message' => 'User Login Successful', 'accessToken' => $accessToken], 200  );
            } else {
                return response()->json(['status' => false, 'message' => 'Either email/password is incorrect.'], 401);
            }
        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 401);
        }
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

    public function changePassword(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Check if the old password matches
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Your Old Password is Incorrect, please try again.',
            ], 401);
        }

        // Update the user's password
        $user->password = Hash::make($request->new_password);

        return response()->json([
            'status' => true,
            'message' => 'You have successfully changed your password.',
        ]);
    }
    public function processForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // Send Email here

        return response()->json(['message' => 'Please check your inbox to reset your password'], 200);
    }

    public function resetPassword(Request $request)
    {
        $token = $request->token;

        $tokenExist = DB::table('password_reset_tokens')->where('token', $token)->first();

        if ($tokenExist == null) {
            return response()->json(['error' => 'Invalid Request'], 404);
        }

        return response()->json(['token' => $token], 200);
    }

    public function processResetPassword(Request $request)
    {
        $token = $request->token;

        $tokenObj = DB::table('password_reset_tokens')->where('token', $token)->first();

        if ($tokenObj == null) {
            return response()->json(['error' => 'Invalid Request'], 404);
        }

        $user = User::where('email', $tokenObj->email)->first();

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return response()->json(['message' => 'You have successfully updated your password.'], 200);
    }
}
