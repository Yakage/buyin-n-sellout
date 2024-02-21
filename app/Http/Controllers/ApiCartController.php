<?php

namespace App\Http\Controllers\Api;

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
        $product = Product::with('product_images')->find($request->id);

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ]);
        }

        if (Cart::count() > 0) {
            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }

            if (!$productAlreadyExist) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty
                ($product->product_images)) ? $product->product_images->first() : '']);

                return response()->json([
                    'status' => true,
                    'message' => '<strong>'.$product->title.'</strong> added to cart successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $product->title.'Already added to cart'
                ]);
            }
        } else { 
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            return response()->json([
                'status' => true,
                'message' => '<strong>'.$product->title.'</strong> added to cart successfully'
            ]);
        }
    }

    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);

        if ($product->track_qty == 'Yes') {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                return response()->json([
                    'status' => true,
                    'message' => 'Cart updated successfully.'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Requested qty('.$qty.') not available in stock.'
                ]);
            }
        } else {
            Cart::update($rowId, $qty);
            return response()->json([
                'status' => true,
                'message' => 'Cart updated successfully.'
            ]);
        }
    }

    public function deleteItem(Request $request)
    {
        $rowId = $request->rowId;
        $itemInfo = Cart::get($rowId);

        if ($itemInfo == null) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found in cart.'
            ]);
        }

        Cart::remove($request->rowId);
        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart successfully'
        ]);
    }
}
