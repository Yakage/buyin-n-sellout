<?php

namespace App\Http\Controllers\web\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class TempImagesController extends Controller
{

    public function create(Request $request)
    {
        // $directory = public_path('temp/thumb');

        // // Check if the directory exists, if not, create it
        // if (!File::isDirectory($directory)) {
        //     File::makeDirectory($directory, 0755, true, true);
        // }
        // Check if image is present in the request
        if ($request->hasFile('image')) {
            // Get the uploaded image
            $image = $request->file('image');

            // Generate unique filename
            $newFileName = time() . '.' . $image->getClientOriginalExtension();

            // Save the original image
            $image->move(public_path('/temp'), $newFileName);

            // Generate thumbnail
            $thumbnail = Image::make(public_path('/temp') . '/' . $newFileName);
            $thumbnail->fit(300, 275);
            $thumbnail->save(public_path('/temp/thumb') . '/' . $newFileName);

            // Create a new TempImage record
            $tempImage = new TempImage();
            $tempImage->name = $newFileName;
            $tempImage->save();

            // Return JSON response
            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'ImagePath' => asset('/temp/thumb/' . $newFileName),
                'message' => 'Image uploaded successfully'
            ]);
        }

        // Return error response if no image is present in the request
        return response()->json([
            'status' => false,
            'message' => 'No image uploaded'
        ]);
    }

    // public function create(Request $request) {

    //     // if ($request->image) {
    //     //     $image = $request->image;
    //     //     $ext = $image->getClientOriginalExtension();
    //     //     $newFileName = time().'.'.$ext;

    //     //     $tempImage = new TempImage;
    //     //     $tempImage->name = $newFileName;
    //     //     $tempImage->save();

    //     //     $image->move(public_path('/uploads/temp/'),$newFileName);

    //     //     return response()->json([
    //     //         'status' => true,
    //     //         'name' => $newFileName,
    //     //         'id' => $tempImage->id,
    //     //         //'image_id' => $tempImage->id,
    //     //         // 'ImagePath' => asset('/temp/thumb/'.$newFileName),
    //     //         'message' => 'Image uploaded successfully'
    //     //     ]);
    //     // }

    //     $image = $request->image;

    //     if(!empty($image)) {
    //         $ext = $image->getClientOriginalExtension();
    //         $newName = time().'.'.$ext;

    //         $tempImage = new TempImage();
    //         $tempImage->name = $newName;
    //         $tempImage->save();

    //         $image->move(public_path().'/temp', $newName);

    //         //generate thumbnail
    //         $sourcePath = public_path().'/temp/'.$newName;
    //         $destPath = public_path().'/temp/thumb/'.$newName;
    //         $image = Image::make($sourcePath);
    //         $image->fit(300,275);
    //         $image->save($destPath);


    //         return response()->json([
    //             'status' => true,
    //             'image_id' => $tempImage->id,
    //             'ImagePath' => asset('/temp/thumb/'.$newName),
    //             'message' => 'Image uploaded successfully'
    //         ]);
    //     }
    // }
}
