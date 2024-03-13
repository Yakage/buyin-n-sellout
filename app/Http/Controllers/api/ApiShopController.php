<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null) {
        $categorySelected = '';
        $subCategorySelected = '';
        $brandsArray = [];
    
        $categories = Category::orderBy('name', 'ASC')->with('sub_category')->where('status', 1)->get();
        $brands = Brand::orderBy('name', 'ASC')->where('status', 1)->get();
        $productsQuery = Product::orderBy('id', 'DESC')->where('status', 1);
    
        // Filter by category
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $productsQuery->where('category_id', $category->id);
                $categorySelected = $category->id;
            }
        }
    
        // Filter by subcategory
        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            if ($subCategory) {
                $productsQuery->where('sub_category_id', $subCategory->id);
                $subCategorySelected = $subCategory->id;
            }
        }
    
        // Filter by brand
        if (!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
            $productsQuery->whereIn('brand_id', $brandsArray);
        }
    
        // Filter by price range
        if ($request->has('price_max') && $request->has('price_min')) {
            $priceMax = intval($request->get('price_max'));
            $priceMin = intval($request->get('price_min'));
    
            if ($priceMax == 1000) {
                $productsQuery->whereBetween('price', [$priceMin, 1000000]);
            } else {
                $productsQuery->whereBetween('price', [$priceMin, $priceMax]);
            }
        }
    
        $products = $productsQuery->get();
    
        $data = [
            'categories' => $categories,
            'brands' => $brands,
            'products' => $products,
            'categorySelected' => $categorySelected,
            'subCategorySelected' => $subCategorySelected,
            'brandsArray' => $brandsArray,
            'priceMax' => $request->get('price_max', 1000),
            'priceMin' => $request->get('price_min', 0),
            'sort' => $request->get('sort')
        ];
    
        return response()->json($data);
    }

    public function product($slug) {
        $product = Product::where('slug', $slug)
            ->withCount('product_ratings')
            ->withSum('product_ratings', 'rating')
            ->with(['product_images', 'product_ratings'])
            ->first();
    
        if ($product == null) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        $relatedProducts = [];
        // Fetch related products
        if ($product->related_products != '') {
            $productArray = explode(',', $product->related_products);
            $relatedProducts = Product::whereIn('id', $productArray)
                ->where('status', 1)
                ->get();
        }
    
        // Rating calculations
        $avgRating = '0.00';
        $avgRatingPer = '0';
        if ($product->product_ratings_count > 0) {
            $avgRating = number_format(($product->product_ratings_sum_rating / $product->product_ratings_count), 2);
            $avgRatingPer = ($avgRating * 100) / 5;
        }
    
        $data = [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'avgRating' => $avgRating,
            'avgRatingPer' => $avgRatingPer
        ];
    
        return response()->json($data);
    }
    
    public function saveRating($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5',
            'email' => 'required|email',
            'comment' => 'required|min:10',
            'rating' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        $count = ProductRating::where('email', $request->email)->count();
        if ($count > 0) {
            return response()->json([
                'status' => false,
                'message' => 'You already rated this product.'
            ], 400);
        }
    
        $productRating = new ProductRating;
        $productRating->product_id = $id;
        $productRating->username = $request->name;
        $productRating->email = $request->email;
        $productRating->comment = $request->comment;
        $productRating->rating = $request->rating;
        $productRating->status = 0;
        $productRating->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Thanks for your rating.'
        ], 201);
    }
}
