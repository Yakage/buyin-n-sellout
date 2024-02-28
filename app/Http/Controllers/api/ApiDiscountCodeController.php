<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ApiDiscountCodeController extends Controller
{
    public function index(Request $request)
    {
        $discountCoupons = DiscountCoupon::latest();

        if (!empty($request->get('keyword'))) {
            $discountCoupons = $discountCoupons->where('name', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('code', 'like', '%' . $request->get('keyword') . '%');
        }

        $discountCoupons = $discountCoupons->paginate(10);

        return response()->json(['discountCoupons' => $discountCoupons]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
            // Add other validation rules here
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Your store logic goes here

        return response()->json(['message' => 'Discount coupon added successfully.']);
    }

    public function edit(Request $request, $id)
    {
        $coupon = DiscountCoupon::find($id);

        if ($coupon == null) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        return response()->json(['coupon' => $coupon]);
    }

    public function update(Request $request, $id)
    {
        $discountCode = DiscountCoupon::find($id);

        if ($discountCode == null) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
            // Add other validation rules here
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the discount coupon properties
        $discountCode->code = $request->code;
        $discountCode->name = $request->name;
        $discountCode->description = $request->description;
        $discountCode->max_users = $request->max_users;
        $discountCode->max_uses_user = $request->max_uses_user;
        $discountCode->type = $request->type;
        $discountCode->discount_amount = $request->discount_amount;
        $discountCode->min_amount = $request->min_amount;
        $discountCode->status = $request->status;
        $discountCode->starts_at = $request->starts_at;
        $discountCode->expires_at = $request->expires_at;
        
        // Save the updated discount coupon
        $discountCode->save();

        return response()->json(['message' => 'Discount coupon updated successfully.']);
    }
    public function destroy(Request $request, $id)
    {
        $discountCode = DiscountCoupon::find($id);

        if ($discountCode == null) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $discountCode->delete();

        return response()->json(['message' => 'Discount coupon deleted successfully.']);
    }
}
