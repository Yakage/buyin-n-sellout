<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiFrontController extends Controller
{
    public function index()
    {
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
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts
        ]);
    }

    public function addToWishList(Request $request)
    {
        if (Auth::check() == false) {
            return response()->json(['status' => false]);
        }

        $product = Product::find($request->id);

        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ]);
        }

        Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id,
            ],
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => '"' . $product->title . '" added to your wishlist.'
        ]);
    }
}
