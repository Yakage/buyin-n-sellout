<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Gloudemans\Shoppingcart\Facades\Cart;
use Carbon\Carbon;

class ApiCartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid product ID',
            ], 422);
        }

        $product = Product::with('product_images')->find($request->id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $cartContent = Cart::content();
        $productAlreadyExist = $cartContent->contains('id', $product->id);

        if (!$productAlreadyExist) {
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => $product->product_images->first() ?? '']);

            return response()->json([
                'status' => true,
                'message' => $product->title . ' added in your cart successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => $product->title . ' already added in cart',
            ]);
        }
    }

    public function cart()
    {
        $cartContent = Cart::content();
        
        $data['cartContent'] = $cartContent;

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function updateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rowId' => 'required',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors(),
            ], 422);
        }

        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);

        if (!$itemInfo) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found',
            ], 404);
        }

        $product = Product::find($itemInfo->id);

        // Check qty available in stock
        if ($product->track_qty == 'Yes') {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);

                return response()->json([
                    'status' => true,
                    'message' => 'Cart updated successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Requested qty (' . $qty . ') not available in stock',
                ]);
            }
        } else {
            Cart::update($rowId, $qty);

            return response()->json([
                'status' => true,
                'message' => 'Cart updated successfully',
            ]);
        }
    }

    public function deleteItem(Request $request)
    {
        $itemInfo = Cart::get($request->rowId);

        if ($itemInfo === null) {
            $errorMessage = 'Item not found in cart';
            return response()->json([
                'status' => false,
                'message' => $errorMessage,
            ], 404);
        }

        Cart::remove($request->rowId);

        $message = 'Item removed from cart successfully';

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }

    public function checkout(Request $request)
    {
        $discount = 0;

        //-- If cart is empty, return an error response
        if (Cart::count() == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty',
            ], 400);
        }

        //-- If the user is not logged in, return a redirect URL to the login page
        if (Auth::check() == false) {
            return response()->json([
                'status' => false,
                'message' => 'User not logged in',
                'redirect_url' => route('account.login'),
            ], 401);
        }

        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();

        // $countries = Country::orderBy('name', 'ASC')->get();

        $subTotal = Cart::subtotal(2, '.', '');

        // Apply discount here
        // if(session()->has('code')) {
        //     $code = session()->get('code');
        //     if($code->type == 'percent') {
        //         $discount = ($code->discount_amount / 100) * $subTotal;
        //     } else {
        //         $discount = $code->discount_amount;
        //     }
        // }

        // Calculate shipping fee
        if ($customerAddress != '') {
            // $userCountry = $customerAddress->country_id;
            // $shippingInfo = ShippingCharge::where('country_id', $userCountry)->first();
            $totalQty = 0;
            // $totalShippingCharge = 0;
            $grandTotal = 0;

            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            // $totalShippingCharge = $totalQty * $shippingInfo->amount;
            $totalShippingCharge = 50;
            // $grandTotal = ($subTotal-$discount) + $totalShippingCharge;
            $grandTotal = $subTotal + $totalShippingCharge;
        } else {
            $grandTotal = ($subTotal - $discount);
            $totalShippingCharge = 0;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'customer_address' => $customerAddress,
                'total_shipping_charge' => $totalShippingCharge,
                'grand_total' => $grandTotal,
            ],
        ]);
    }

    
}
