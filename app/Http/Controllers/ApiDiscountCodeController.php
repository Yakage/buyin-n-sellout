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

        return response()->json($discountCoupons);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            // Check if the start date is greater than the current date
            if (!empty($request->starts_at)) {
                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);

                if ($startAt->lte($now)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date cannot be less than current time.']
                    ]);
                }
            }

            // Check if the expiry date is greater than the start date
            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);

                if (!$expiresAt->gt($startAt)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date must be greater than start date.']
                    ]);
                }
            }

            $discountCode = new DiscountCoupon();
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
            $discountCode->save();

            return response()->json([
                'status' => true,
                'message' => 'Discount coupon added successfully.'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $discountCode = DiscountCoupon::find($id);

        if ($discountCode == null) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            // Check if the expiry date is greater than the start date
            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);

                if (!$expiresAt->gt($startAt)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date must be greater than start date.']
                    ]);
                }
            }

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
            $discountCode->save();

            return response()->json([
                'status' => true,
                'message' => 'Discount coupon updated successfully.'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        $discountCode = DiscountCoupon::find($id);

        if ($discountCode == null) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ]);
        }

        $discountCode->delete();

        return response()->json([
            'status' => true,
            'message' => 'Discount coupon deleted successfully.'
        ]);
    }
}
