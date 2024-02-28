<?php

namespace App\Http\Controllers\web\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;

class TempImagesController extends Controller
{
    public function create(Request $request) {

        if ($request->image) {
            $image = $request->image;
            $extension = $image->getClientOriginalExtension();
            $newFileName = time().'.'.$extension;

            $tempImage = new TempImage();
            $tempImage->name = $newFileName;
            $tempImage->save();

            $image->move(public_path('uploads/temp'),$newFileName);

            return response()->json([
                'status' => true,
                'name' => $newFileName,
                'id' => $tempImage->id
            ]);
        }
        // $image = $request->image;

        // if(!empty($image)) {
        //     $ext = $image->getClientOriginalExtension();
        //     $newName = time().'.'.$ext;

        //     $tempImage = new TempImage();
        //     $tempImage->name = $newName;
        //     $tempImage->save();

        //     $image->move(public_path().'/temp', $newName);

        //     //generate thumbnail
        //     $sourcePath = public_path().'/temp/'.$newName;
        //     $destPath = public_path().'/temp/thumb/'.$newName;
        //     $image = Image::make($sourcePath);
        //     $image->fit(300,275);
        //     $image->save($destPath);


        //     return response()->json([
        //         'status' => true,
        //         'image_id' => $tempImage->id,
        //         'ImagePath' => asset('/temp/thumb/'.$newName),
        //         'message' => 'Image uploaded successfully'
        //     ]);
        //}
    }
}