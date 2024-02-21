<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request) {

        $orders = Order::latest();

        if($request ->get('keyword') !== ""){
            $orders = $orders->leftJoin('users','users.id','orders.user_id');
        }
        return view('admin.orders.list');
    }

    public function detail() {
        
    }
}
