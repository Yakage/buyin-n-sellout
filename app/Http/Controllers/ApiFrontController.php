<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ApiFrontController extends Controller
{
    public function index() {
        $featuredProducts = Product::where('is_featured', 'Yes')
                                    ->orderBy('id', 'DESC')
                                    ->take(8)
                                    ->where('status', 1)
                                    ->get();

        $latestProducts = Product::orderBy('id', 'DESC')
                                    ->where('status', 1)
                                    ->take(8)
                                    ->get();

        return response()->json([
            'status' => true,
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts
        ]);
    }
}
