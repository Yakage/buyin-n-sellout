<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;


class ApiTempImagesController extends Controller
{
    public function create(Request $request)
    {
        // $directory = public_path('temp/thumb');

        // // Check if the directory exists, if not, create it
        // if (!File::isDirectory($directory)) {
        //     File::makeDirectory($directory, 0755, true, true);
        // }

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
}
