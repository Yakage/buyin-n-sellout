<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ApiProductController extends Controller
{

    public function index(Request $request) {
        $products = Product::latest('id')->with('product_images');

        if($request->get('keyword') != "") {
            $products = $products->where('title','like','%'.$request->keyword.'%');
        }

        $products = $products->paginate();
        return response()->json($products);
        
    }

    public function create() {
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        return response()->json([
            'categories' => $categories,
            'brands' => $brands
        ]);
    }

    public function store(Request $request) {

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];
    
        if (!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $product = new Product;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->shipping_returns = $request->shipping_returns;
            $product->short_description = $request->short_description;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $product->save();

            //save gallery images
            if(!empty($request->image_array)) {
                foreach ($request->image_array as $temp_image_id) {

                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.',$tempImageInfo->name);
                    //1707902111.png
                    $ext = last($extArray); //like jpg,gif,png etc

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //generate product thumbnails

                    //large img

                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;                  
                    $destPath = public_path().'/uploads/product/large/'.$imageName;                  
                    $image = Image::make($sourcePath);
                    $image->resize(1400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);

                    //small img
                    $destPath = public_path().'/uploads/product/small/'.$imageName;                  
                    $image = Image::make($sourcePath);
                    $image->resize(300, 300);
                    $image->save($destPath);
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Product added successfully.'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request) {
        $product = Product::find($id);

        if (empty($product)) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }
        
        // fetch product imgaes
        $productImages = ProductImage::where('product_id',$product->id)->get();

        $subCategories = SubCategory::where('category_id',$product->category_id)->get();

        $relatedProducts = [];
        // fetch related products
        if($product->related_products != '') {
            $productArray = explode(',', $product->related_products);

            $relatedProducts = Product::whereIn('id', $productArray)->get();
        }

        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        
        return response()->json([
            'categories' => $categories,
            'brands' => $brands,
            'product' => $product,
            'subCategories' => $subCategories,
            'productImages' => $productImages,
            'relatedProducts' => $relatedProducts
        ]);
    }

    public function update($id, Request $request) {
        $product = Product::find($id);

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,slug,'.$product->id.',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];
    
        if (!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->shipping_returns = $request->shipping_returns;
            $product->short_description = $request->short_description;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $product->save();
            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully.'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id, Request $request) {
        $product = Product::find($id);

        if (empty($product)) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        $productImages = ProductImage::where('product_id',$id)->get();

        if (!empty($productImages)) {
            foreach ($productImages as $productImage) {
                File::delete(public_path('uploads/product/large/'.$productImage->image));
                File::delete(public_path('uploads/product/small/'.$productImage->image));
            }

            $productImages = ProductImage::where('product_id',$id)->delete();

        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    public function getProducts(Request $request) {
        $tempProduct = [];

        if($request->term != "") {
            $products = Product::where('name', 'like', '%'.$request->term.'%')->get();

            if($products != null) {
                foreach ($products as $product) {
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }

        return response()->json([
            'tags' => $tempProduct,
            'status' => true
        ]);
    }

    public function getProductRatings(Request $request)
    {
        $ratings = ProductRating::select('product_ratings.*', 'products.title as productTitle')
            ->orderBy('product_ratings.created_at', 'DESC')
            ->leftJoin('products', 'products.id', 'product_ratings.product_id');

        if ($request->get('keyword') != "") {
            $ratings->orWhere('products.title', 'like', '%' . $request->keyword . '%')
                ->orWhere('product_ratings.username', 'like', '%' . $request->keyword . '%');
        }

        $ratings = $ratings->paginate(10);

        return response()->json([
            'ratings' => $ratings
        ]);
    }

    public function changeRatingStatus(Request $request)
    {
        $productRating = ProductRating::find($request->id);
        
        if (!$productRating) {
            return response()->json([
                'status' => false,
                'message' => 'Product rating not found'
            ], 404);
        }

        $productRating->status = $request->status;
        $productRating->save();

        return response()->json([
            'status' => true,
            'message' => 'Status changed successfully'
        ]);
    }
}
