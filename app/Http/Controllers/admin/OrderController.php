<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request) {

        $orders = Order::latest();
        $orders = $orders->leftJoin('users','users.id','orders.user_id');

        if($request ->get('keyword') !== ""){
        $orders = $orders->where('users.name','like','%'.$request->keyword.'%');
        $orders = $orders->orWhere('users.email','like','%'.$request->keyword.'%');
        $orders = $orders->orWhere('orders.id','like','%'.$request->keyword.'%');
        }

        $orders = $orders->paginate(10);

        ///$data('orders')=$orders;
        return view('admin.orders.list',[
            'orders' => $orders
        ]);
    }

    public function detail() {
        
    }
}
