<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ApiOrderController extends Controller
{
    public function index(Request $request) {
        $orders = Order::latest('orders.created_at')->select('orders.*', 'users.name', 'users.email')
            ->leftJoin('users', 'users.id', 'orders.user_id');

        if ($request->get('keyword') !== "") {
            $orders->where('users.name', 'like', '%' . $request->keyword . '%')
                ->orWhere('users.email', 'like', '%' . $request->keyword . '%')
                ->orWhere('orders.id', 'like', '%' . $request->keyword . '%');
        }

        $orders = $orders->paginate(10);

        return response()->json([
            'orders' => $orders
        ]);
    }

    public function detail($orderId) {
        $order = Order::select('orders.*', 'countries.name as countryName')
            ->where('orders.id', $orderId)
            ->leftJoin('countries', 'countries.id', 'orders.country_id')
            ->first();

        $orderItems = OrderItem::where('order_id', $orderId)->get();

        return response()->json([
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }

    public function changeOrderStatus(Request $request, $orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();

        $message = 'Order status updated successfully';

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function sendInvoiceEmail(Request $request, $orderId)
    {
        // Assuming orderEmail is a function that sends the email
        // You might need to modify this based on your actual implementation
        orderEmail($orderId, $request->userType);

        $message = 'Order email sent successfully';

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
}
