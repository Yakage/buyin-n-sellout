<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiShippingController extends Controller
{
    public function create() {
        $countries = Country::get();
        $shippingCharges = ShippingCharge::select('shipping_charges.*','countries.name')
            ->leftJoin('countries', 'countries.id', 'shipping_charges.country_id')
            ->get();

        return response()->json([
            'status' => true,
            'countries' => $countries,
            'shippingCharges' => $shippingCharges
        ]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {
            $count = ShippingCharge::where('country_id', $request->country)->count();

            if ($count > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Shipping already added.'
                ]);
            }

            $shipping = new  ShippingCharge();
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            return response()->json([
                'status' => true,
                'message' => 'Shipping added successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id) {
        $shippingCharge = ShippingCharge::find($id);
        
        if (!$shippingCharge) {
            return response()->json([
                'status' => false,
                'message' => 'Shipping not found.'
            ]);
        }

        $countries = Country::get();

        return response()->json([
            'status' => true,
            'shippingCharge' => $shippingCharge,
            'countries' => $countries
        ]);
    }

    public function update($id, Request $request) {
        $shipping = ShippingCharge::find($id);

        if (!$shipping) {
            return response()->json([
                'status' => false,
                'message' => 'Shipping not found.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            return response()->json([
                'status' => true,
                'message' => 'Shipping updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id) {
        $shippingCharge = ShippingCharge::find($id);

        if (!$shippingCharge) {
            return response()->json([
                'status' => false,
                'message' => 'Shipping not found.'
            ]);
        }

        $shippingCharge->delete();

        return response()->json([
            'status' => true,
            'message' => 'Shipping deleted successfully.'
        ]);
    }
}
