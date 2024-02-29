<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ApiProductImageController extends Controller
{
    public function getImage($id)
    {
        $productImage = ProductImage::find($id);

        if (empty($productImage)) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $imagePath = public_path('/uploads/product/small/' . $productImage->image);

        // Check if the image file exists
        if (File::exists($imagePath)) {
            $imageContents = file_get_contents($imagePath);

            // Return the image response
            return response($imageContents, Response::HTTP_OK)
                ->header('Content-Type', 'image/*');
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Image file not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
    public function update(Request $request) {

        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = null; // We'll update this later
        $productImage->save();

        $imageName = $request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
        $productImage->image = $imageName;
        $productImage->save();

        //large img
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

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'ImagePath' => asset('uploads/product/small/'.$productImage->image),
            'message' => 'Image saved successfully'
        ]);
    }

    public function destroy(Request $request) {
        $productImage = ProductImage::find($request->id);

        if (empty($productImage)) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ], 404);
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
