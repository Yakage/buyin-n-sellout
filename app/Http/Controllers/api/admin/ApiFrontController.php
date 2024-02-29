<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactEmail;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->first();
        if ($page == null) {
            return response()->json(['error' => 'Page not found.'], 404);
        }
        return response()->json(['page' => $page]);
    }

    public function sendContactEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required|min:10',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        // Send email here

        $mailData = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'mail_subject' => 'You have received a contact email',
        ];

        $admin = User::find(1); // Assuming the admin user's ID is 1

        Mail::to($admin->email)->send(new ContactEmail($mailData));

        return response()->json(['status' => true, 'message' => 'Thanks for contacting us, we will get back to you soon.']);
    }
}
