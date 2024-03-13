<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\ContactEmail;
use App\Mail\ContactFormMail;
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
    public function index(Request $request) {
        // Fetch featured products
        $featuredProducts = Product::where('is_featured', 'Yes')
                                    ->orderBy('id', 'DESC')
                                    ->where('status', 1)
                                    ->get();
    
        // Fetch latest products
        $latestProducts = Product::orderBy('id', 'DESC')
                                 ->where('status', 1)
                                 ->take(8)
                                 ->get();
    
        // Prepare response data
        $responseData = [
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts
        ];
    
        // Return data in JSON format
        return response()->json($responseData);
    }

    public function addToWishList(Request $request) {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated.'
            ]);
        }
    
        // Fetch the product by ID
        $product = Product::find($request->id);
    
        // Check if the product exists
        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ]);
        }
    
        // Update or create a wishlist entry for the user and product
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
            'message' => 'Product added to wishlist.',
            'product' => $product // Optionally return the added product
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

    public function sendContactEmail(Request $request){
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'email'=> 'required|email',
            'subject' => 'required|min:10',
            'message' => 'required'
        ]);
    
        // If validation passes, send email
        if ($validator->passes()) {
            $mailData = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'mail_subject' =>'You have received a contact email',
            ];
    
            // Fetch admin user
            $admin = User::where('id', 1)->first();
    
            // Send email
            Mail::to($admin->email)->send(new ContactEmail($mailData));
    
            // Flash success message
            session()->flash('success', 'Thanks for contacting us, we will get back to you soon.');
    
            return response()->json([
                'status'=> true,
            ]);
        } else {
            // Return validation errors
            return response()->json([
                'status'=> false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function viewAboutUs(){
        return response()->json([
            'view' => 'aboutus'
        ]);
    }
    
    public function viewContactUs() {
        return response()->json([
            'view' => 'contactus'
        ]);
    }
    public function processContactUs(Request $request) {
        // Validate form data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);
    
        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    
        // Send email
        try {
            Mail::to($request->email)->send(new ContactFormMail($request));
            return response()->json([
                'status' => true,
                'message' => 'Message sent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send message. Please try again later.'
            ]);
        }
    }
}
