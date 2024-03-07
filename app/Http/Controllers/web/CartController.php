<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingCharge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request){
    //     $product = Product::with('product_images')->find($request->product_id);

    // if ($product == null) {
    //     if ($request->ajax()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Product not found',
    //             'redirect' => route('front.cart')
    //         ]);
    //     } else {
    //         return redirect()->route('front.cart')->with('error', 'Product not found');
    //     }
    // }

    // if (Cart::count() > 0) {
    //     $cartContent = Cart::content();
    //     $productAlreadyExist = false;

    //     foreach ($cartContent as $item) {
    //         if ($item->id == $product->id) {
    //             $productAlreadyExist = true;
    //         }
    //     }

    //     if ($productAlreadyExist == false) {
    //         Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);

    //         $status = true;
    //         $message = '<strong>' . $product->title . '</strong> added in your cart successfully';

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'status' => $status,
    //                 'message' => $message,
    //                 'redirect' => route('front.home')
    //             ]);
    //         } else {
    //             session()->flash('success', $message);
    //             return redirect()->route('front.home');
    //         }
    //     } else {
    //         $status = false;
    //         $message = $product->title . ' already added in cart';

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'status' => $status,
    //                 'message' => $message,
    //                 'redirect' => route('front.cart')
    //             ]);
    //         } else {
    //             return redirect()->route('front.cart')->with('error', $message);
    //         }
    //     }
    // } else {
    //     Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
    //     $status = true;
    //     $message = '<strong>' . $product->title . '</strong> added in your cart successfully';

    //     if ($request->ajax()) {
    //         return response()->json([
    //             'status' => $status,
    //             'message' => $message,
    //             'redirect' => route('front.cart')
    //         ]);
    //     } else {
    //         session()->flash('success', $message);
    //         return redirect()->route('front.cart');
    //     }
    // }
        $product = Product::with('product_images')->find($request->id);
        
        if ( $product==null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'redirect' => route('front.cart')
            ]);

        }

        if(Cart::count()>0) {
            //echo "Product already in cart";
            //Products found in cart
            //Check if this product already in the cart
            //Return a message that product already added in your cart
            //If product not found in the cart, then add product in cart

            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item){
                if($item->id == $product->id){
                    $productAlreadyExist = true;
                }
            }

            if($productAlreadyExist == false){
                Cart::add($product->id, $product->title, 1,$product->price, ['productImage' => (!empty
                ($product->product_images))? $product->product_images->first() : '']);

                $status = true;
                $message = '<strong>'.$product->title.'</strong> added in your cart successfully';

                session()->flash('success' ,$message);
                return redirect()->route('front.home');

            } else {
                $status = false;
                $message = $product->title.' already added in cart';
                return redirect()->route('front.cart');
            }

            

        } else {
            Cart::add($product->id, $product->title, 1,$product->price, ['productImage' => (!empty
            ($product->product_images))? $product->product_images->first() : '']);
            $status =true;
            $message ='<strong>' .$product->title.'</strong> added in your cart successfully';
            session()->flash('success' ,$message);
            //return redirect()->route('front.cart');

        }

        return response()->json([
            'status' =>  $status,
            'message' =>  $message,
            'redirect' => route('front.cart')
        ]);

        
    }

    public function cart(){
        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;
        return view('front.cart',$data);
    }
    public function updateCart(Request $request) {
        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);

        $product = Product::find($itemInfo->id);
        // check qty available in stock

        if ($product->track_qty == 'Yes') {

            if($qty<=$product->qty) {
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
                session()->flash('success',$message );
            }else {
                $message = 'Request qty('.$qty.') not available in stock';
                $status = false;
                session()->flash('error',$message );
            }
        } else {
            Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
                session()->flash('success',$message );
        }

        return response()->json([
            'status' => $status,
            'message' => $message 
        ]);
    }

    public function deleteItem(Request $request) {

        $itemInfo = Cart::get($request->rowId);

        if($itemInfo = null) {
            $errorMessage = 'Item not found in cart' ;
            session()->flash('error',$errorMessage );
            return response()->json([
                'status' => false,
                'message' => $errorMessage 
            ]);
        }
        Cart::remove($request->rowId);

        $message = 'Item removed from cart successfully';
        session()->flash('success',$message );
        return response()->json([
            'status' => true,
            'message' => $message 
        ]);
    }

    public function checkout() {
        $discount = 0;

        //-- if cart empty, redirect to cart page
        if(Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        //-- if user not logged in, redirect to login page
        if(Auth::check() == false) {

            if(!session()->has('url.intended')){
                session(['url.intended' => url()->current()]);
            }

            return redirect()->route('account.login');
        }

        $customerAddress = CustomerAddress::where('user_id',$user = Auth::user()->id)->first();



        session()->forget('url.intended');

        $countries = Country::orderBy('name', 'ASC')->get();

        $subTotal = Cart::subtotal(2,'.','');

        //apply discount here
        if(session()->has('code')) {

            $code = session()->get('code');
            if($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
        }

        //calculate shipping fee
        if($customerAddress != '') {
            // $userCountry = $customerAddress->country_id;
            // $shippingInfo = ShippingCharge::where('country_id', $userCountry)->first();
    
            $totalQty = 0;
            $totalShippingCharge = 0;
            $grandTotal = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }
    
            // $totalShippingCharge = $totalQty * $shippingInfo->amount;
            $totalShippingCharge = $totalQty + 50;
            // $grandTotal = ($subTotal-$discount) + $totalShippingCharge;
            $grandTotal = $subTotal + $totalShippingCharge;
        } else {
            $grandTotal = ($subTotal-$discount);
            $totalShippingCharge = 0;


        }

        return view('front.checkout', [
            // 'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'discount' => $discount,
            'grandTotal' => $grandTotal
        ]);
    }

    public function processCheckout(Request $request) {
        
        // step1 apply validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            //'country' => 'required',
            'address' => 'required|min:15',
            'apartment' => 'nullable', 
            'city' => 'required',
            'barangay' => 'required',
            'zip' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please fix the errors',
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        //step 2 save user address

        $user = Auth::user();

        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request-> last_name,
                'email' => $request-> email,
                'mobile' => $request-> mobile,
                //'country_id' => $request-> country,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'barangay' => $request->has('barangay') ? $request->barangay : '',
                'zip' => $request->zip,
            ]
        );

        //step 3 store data in orders table
        
        if($request->payment_method == 'cod') {

            $discountCodeId = NULL;
            $promoCode   = '';
            $shipping = 0;
            $discount = 0;
            $subTotal = Cart::subtotal(2,'.','');
            $grandTotal = $subTotal + $shipping;

            //apply discount here
            if(session()->has('code')) {
                $code = session()->get('code');
                if($code->type == 'percent') {
                    $discount = ($code->discount_amount / 100) * $subTotal;
                } else {
                    $discount = $code->discount_amount;
                }

                $discountCodeId = $code->id;
                $promoCode = $code->code;
            }


            //calculate shipping
            $shippingInfo = ShippingCharge::where('country_id', $request->country)->first();
            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if($shippingInfo != null) {
                $shipping = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shipping;

            } else {
                $shippingInfo = ShippingCharge::where('country_id', $request->rest_of_world)->first();
                $shipping = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shipping;
            }

            

            $order = new Order();
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
            $order->discount = $discount;
            $order->coupon_code_id = $discountCodeId;
            $order->coupon_code = $promoCode;
            $order->payment_status = 'not paid';
            $order->status = 'pending';
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->barangay = $request->barangay;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->notes = $request->order_notes;
            //$order->country_id = $request->country;
            $order->save();


            // step 4 - store order items in order items table

            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->name;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price*$item->qty;
                $orderItem->save();

                //Update Product Stock
                $productData = Product::find($item->id);
                if($productData->track_qty =='Yes'){
                    $currentQty = $productData->qty;
                    $updatedQty = $currentQty-$item->qty;
                    $productData->qty = $updatedQty;
                    $productData->save();
                }
            }

            //Send Order Email
            orderEmail($order->id,'customer');

            session()->flash('success', 'You have successfully placed your order.');

            Cart::destroy();

            session()->forget('code');
            return response()->json([
                'message' => 'Order saved successfully',
                'order' => $order->id,
                'status' => true,
                'redirect' => route('front.thankyou')
            ]);
        } else {
            //
        }

    }

    public function thankyou($id) {

        return view('front.thanks', [
            'id' => $id
        ]);

    }
    public function getOrderSummary(Request $request) {

        $subTotal = Cart::subtotal(2, '.', '');
        $discount = 0;
        $discountString = '';


        //apply discount here
        if(session()->has('code')) {
            $code = session()->get('code');
            if($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }

            $discountString = `<div class=" mt-4" id="discount-response">
                <strong>'.session()->get('code')->code.'</strong>
                <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
            </div>`;
        }

        

        if($request->country_id > 0) {

            $shippingInfo = Shippingcharge::where('country_id', $request->country_id)->first();

            $totalQty = 0;
            foreach (Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if($shippingInfo != null) {

                $shippingCharge = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2),
                ]);
            } else {
                $shippingInfo = ShippingCharge::where('country_id', $request->rest_of_world)->first();

                $shippingCharge = $totalQty * $shippingInfo->amount;
                $grandTotal = ($subTotal - $discount) + $shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2),
                ]);

            }
        } else {

            return response()->json([
                'status' => true,
                'grandTotal' => number_format(($subTotal - $discount),2),
                'discount' => number_format($discount,2),
                'discountString' => $discountString,
                'shippingCharge' => number_format(0,2),
            ]);

        }
    }

    public function applyDiscount(Request $request) {

        $code = DiscountCoupon::where('code', $request->code)->first();

        if($code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon',
            ]);
        }

        // check if coupon start data is valid or not

        $now = Carbon::now();



        if($code->starts_at != "") {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->starts_at);

            if($now->lt($startDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon',
                ]);
            }
        }

        if($code->expires_at != "") {
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->expires_at);

            if($now->gt($endDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon',
                ]);
            }
        }

        // Max Uses Check
        if($code->max_uses > 0){
            $couponUsed = Order::where('coupon_code_id', $code->id)->count();

        if($couponUsed >= $code->max_uses) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon',
            ]);
        }
    }

        //Max Uses User Check
        if($code->max_uses_user > 0){
            $couponUsedByUser = Order::where(['coupon_code_id'=> $code->id, 'user_id' => Auth::user()->id])->count();

            if($couponUsedByUser >= $code->max_uses_user) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already used this coupon.',
                ]);
            }
        }

        $subTotal = Cart::subtotal(2,'.','');

        //Min Amount Condition Check

        if ($code->min_amount > 0) {
            if ($subTotal < $code->min_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your min amount must be PHP' .$code->min_amount.'.',
                ]);
            }

        }
        session()->put('code', $code); 

        return $this->getOrderSummary($request);
    }

    public function removeCoupon(Request $request) {
        session()->forget('code');
        return $this->getOrderSummary($request);

    }
}

