<?php

namespace App\Http\Controllers\api\admin;

use ApiResponseTrait;
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

    public function index(Request $request) {
        $products = Product::where('is_featured', 'Yes')->orderBy('id', 'DESC')->where('status', 1)->get();
        $data['featuredProducts'] = $products;

        $latestProducts = Product::orderBy('id', 'DESC')->where('status', 1)->take(8)->get();
        $data['latestProducts'] = $latestProducts;

        return $this->successResponse($data);
    }

    public function addToWishList(Request $request) {
        if (Auth::check() == false) {
            return $this->errorResponse('User not authenticated', 401);
        }

        $product = Product::where('id', $request->id)->first();

        if ($product == null) {
            return $this->errorResponse('Product not found', 404);
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

        return $this->successResponse([
            'status' => true,
            'message' => 'Product added to wishlist',
        ]);
    }

    public function page($slug) {
        $page = Page::where('slug', $slug)->first();

        if ($page == null) {
            return $this->notFoundResponse();
        }

        return $this->successResponse(['page' => $page]);
    }

    public function sendContactEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'email'=> 'required|email',
            'subject' => 'required|min:10'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $mailData = [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'mail_subject' => 'You have received a contact email',
        ];

        $admin = User::where('id', 1)->first();

        Mail::to($admin->email)->send(new ContactEmail($mailData));
        session()->flash('success', 'Thanks for contacting us, we will get back to you soon.');

        return $this->successResponse(['status' => true]);
    }

    public function viewAboutUs(){
        // This method is not suitable for an API, consider returning data directly.
        return $this->errorResponse('Method not allowed', 405);
    }

    public function viewContactUs() {
        // This method is not suitable for an API, consider returning data directly.
        return $this->errorResponse('Method not allowed', 405);
    }
}

