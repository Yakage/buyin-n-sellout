<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Gloudemans\Shoppingcart\Facades\Cart;
use Carbon\Carbon;
use Stripe\Stripe;

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

    public function processCheckout(Request $request) {
        // Step 1: Apply validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'address' => 'required|min:15',
            'apartment' => 'nullable', 
            'city' => 'required',
            'barangay' => 'required',
            'zip' => 'required',
            'payment_method' => 'required|in:cod,stripe', // Ensure payment method is provided and valid
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please fix the errors',
                'status' => false,
                'errors' => $validator->errors()
            ]);
        } 
    
        // Step 2: Save user address
        $user = Auth::user();
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'zip' => $request->zip,
            ]
        );
    
        // Step 3: Store data in orders table
        
    
        if ($request->payment_method == 'cod') {

            $shipping = 50;
            $subTotal = Cart::subtotal(2, '.', '');
            $grandTotal = $subTotal + $shipping;
        
            $order = new Order;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
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
            $order->save();
        
            // Step 4: Store order items in order items table
            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price * $item->qty;
                $orderItem->save();
        
                // Update Product Stock
                $productData = Product::find($item->id);
                if ($productData->track_qty == 'Yes') {
                    $currentQty = $productData->qty;
                    $updatedQty = $currentQty - $item->qty;
                    $productData->qty = $updatedQty;
                    $productData->save();
                }
            }
    
            // Send Order Email
            orderEmail($order->id, 'customer');

            Cart::destroy();
            session()->forget('code');
    
            // Return success response
            return response()->json([
                'message' => 'Order saved successfully',
                'orderId' => $order->id,
                'status' => true,
            ]);
        } else {
            // Calculate shipping and total amount
            $shipping = 50;
            $subTotal = Cart::subtotal(2, '.', '');
            $grandTotal = $subTotal + $shipping;

            // Prepare line items for Stripe checkout
            $productItems = [];
            foreach (Cart::content() as $item) {

                $product_name = $item->name;
                $total = $grandTotal;
                $quantity = $item->qty;
                $two0 = "00";
                $unit_amount = "$total$two0";
     
                $productItems[] = [
                    'price_data' => [
                        'product_data' => [
                            'name' => $product_name,
                        ],
                        'currency'     => 'php',
                        'unit_amount'  => $unit_amount,
                    ],
                    'quantity' => $quantity
                ];
            }

            // Create order and store order items
            $order = new Order;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
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
            $order->save();

            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem;
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price * $item->qty;
                $orderItem->save();

                // Update Product Stock
                $productData = Product::find($item->id);
                if ($productData->track_qty == 'Yes') {
                    $currentQty = $productData->qty;
                    $updatedQty = $currentQty - $item->qty;
                    $productData->qty = $updatedQty;
                    $productData->save();
                }
            }

            // Create Stripe Checkout session
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $checkoutSession = \Stripe\Checkout\Session::create([
                'line_items' => $productItems,
                'mode' => 'payment',
                'allow_promotion_codes' => true,
                'metadata' => [
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                ],
                'customer_email' => $user->email,
                'success_url' => route('front.thankyou', ['orderId' => $order->id]),
            ]);

            // Return success response with Stripe checkout URL
            return response()->json([
                'message' => 'Redirecting to Stripe for payment',
                'checkout_url' => $checkoutSession->url,
            ]);
        }
    }

    public function thankyou($id) {
        return response()->json([
            'message' => 'Thank you for your order',
            'orderId' => $id
        ]);
    }

    public function getOrderSummary(Request $request) {
        $subTotal = Cart::subtotal(2, '.', '');
        $discount = 0;
        $discountString = '';
    
        // Apply discount if available
        if(session()->has('code')) {
            $code = session()->get('code');
            if($code->type == 'percent') {
                $discount = ($code->discount_amount / 100) * $subTotal;
            } else {
                $discount = $code->discount_amount;
            }
    
            $discountString = '<div class="mt-4" id="discount-response">
                <strong>'.session()->get('code')->code.'</strong>
                <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
            </div>';
        }
    
        // Calculate shipping charge
        $shippingCharge = 50; // Default shipping charge
        $grandTotal = $subTotal + $shippingCharge;
    
        return response()->json([
            'status' => true,
            'grandTotal' => number_format($grandTotal, 2),
            'shippingCharge' => number_format($shippingCharge, 2),
        ]);
    }

    public function applyDiscount(Request $request) {
        // Retrieve discount coupon from the database
        $code = DiscountCoupon::where('code', $request->code)->first();
    
        // If discount coupon does not exist, return error response
        if ($code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon',
            ]);
        }
    
        // Check if coupon start date is valid
        $now = Carbon::now();
        if ($code->starts_at != "") {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->starts_at);
            if ($now->lt($startDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon',
                ]);
            }
        }
    
        // Check if coupon expiration date is valid
        if ($code->expires_at != "") {
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->expires_at);
            if ($now->gt($endDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon',
                ]);
            }
        }
    
        // Check maximum uses of coupon
        if ($code->max_uses > 0) {
            $couponUsed = Order::where('coupon_code_id', $code->id)->count();
            if ($couponUsed >= $code->max_uses) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon',
                ]);
            }
        }
    
        // Check maximum uses per user of coupon
        if ($code->max_uses_user > 0) {
            $couponUsedByUser = Order::where(['coupon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();
            if ($couponUsedByUser >= $code->max_uses_user) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already used this coupon.',
                ]);
            }
        }
    
        // Check minimum amount condition
        $subTotal = Cart::subtotal(2, '.', '');
        if ($code->min_amount > 0) {
            if ($subTotal < $code->min_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your min amount must be PHP ' . $code->min_amount . '.',
                ]);
            }
        }
    
        // Store the discount coupon in session
        session()->put('code', $code);
    
        // Return order summary
        return $this->getOrderSummary($request);
    }

    public function removeCoupon(Request $request) {
        session()->forget('code');
        return $this->getOrderSummary($request);
    }
}
