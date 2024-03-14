<?php

namespace App\Http\Controllers\web\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;


class ProductImageController extends Controller
{
    

    public function update(Request $request) {
        try {
            $image = $request->image;
            $extension = $image->getClientOriginalExtension();
            $sourcePath = $image->getPathName();

            $productImage = new ProductImage();
            $productImage->product_id = $request->product_id;

            $imageName = $request->product_id.'-'.$productImage->id.'-'.time().'.'.$extension;
            $productImage->image = $imageName;

            $productImage->save();

            // large img
            $destPath = public_path().'/uploads/product/large/'.$imageName;
            $image = Image::make($sourcePath);
            $image->resize(1400, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->save($destPath);

            // small img
            $destPath = public_path().'/uploads/product/small/'.$imageName;
            $image = Image::make($sourcePath);
            $image->resize(300, 300);
            $image->save($destPath);

            // Logging
            Log::info('Large image saved successfully: '.$destPath);
            Log::info('Small image saved successfully: '.$destPath);

            return response()->json([
                'status' => true,
                'image_id' => $productImage->id,
                'ImagePath' => asset('uploads/product/small/'.$imageName),
                'message' => 'Image saved successfully'
            ]);
        } catch (\Exception $e) {
            // Log any exception
            Log::error('Error updating product image: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error updating product image'
            ]);
        }
    }

    public function destroy(Request $request) {
        $productImage = ProductImage::find($request->id);

        if (empty($productImage)) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ]);
        }
        //delete img from folder

        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));
        $productImage->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully'
        ]);
    }    
}
