<?php

use App\Http\Controllers\api\admin\ApiAdminLoginController;
use App\Http\Controllers\api\admin\ApiBrandController;
use App\Http\Controllers\api\admin\ApiCategoryController;
use App\Http\Controllers\api\admin\ApiDiscountCodeController;
use App\Http\Controllers\api\admin\ApiHomeController;
use App\Http\Controllers\api\admin\ApiOrderController;
use App\Http\Controllers\api\admin\ApiPageController;
use App\Http\Controllers\api\admin\ApiProductController;
use App\Http\Controllers\api\admin\ApiProductImageController;
use App\Http\Controllers\api\admin\ApiProductSubCategoryController;
use App\Http\Controllers\api\admin\ApiSettingController;
use App\Http\Controllers\api\admin\ApiShippingController;
use App\Http\Controllers\api\admin\ApiSubCategoryController;
use App\Http\Controllers\api\admin\ApiUserController;
use App\Http\Controllers\api\ApiAuthController;
use App\Http\Controllers\api\ApiCartController;
use App\Http\Controllers\api\ApiFrontController;
use App\Http\Controllers\api\ApiShopController;
use App\Http\Controllers\web\admin\TempImagesController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//API routes

Route::get('/', [ApiFrontController::class,'index']);
Route::get('/shop{categorySlug?}/{subCategorySlug?}', [ApiShopController::class, 'index']);
Route::get('/product//{slug}', [ApiShopController::class,'product']);
Route::get('/cart', [ApiCartController::class, 'cart']);
Route::post('/add-to-cart', [ApiCartController::class, 'addToCart']);
Route::post('/update-cart', [ApiCartController::class, 'updateCart']);
Route::post('/delete-item', [ApiCartController::class, 'deleteItem']);
Route::get('/checkout', [ApiCartController::class, 'checkout']);
Route::get('/process-checkout', [ApiCartController::class, 'processCheckout']);
Route::get('/thanks/{orderId}', [ApiCartController::class, 'thankyou']);
Route::post('/get-order-summary', [ApiCartController::class, 'getOrderSummary']);
Route::post('/apply-discount', [ApiCartController::class, 'applyDiscount']);
Route::post('/remove-discount', [ApiCartController::class, 'removeCoupon']);
Route::post('/add-to-wishlist', [ApiFrontController::class, 'addToWishList']);
Route::get('/page/{slug}', [ApiFrontController::class, 'page']);
Route::post('/send-contact-email', [ApiFrontController::class, 'sendContactEmail']);

Route::get('/forgot-password', [ApiAuthController::class, 'forgotPassword']);
Route::post('/process-forgot-password', [ApiAuthController::class, 'processForgotPassword']);
Route::get('/reset-password/{token}', [ApiAuthController::class, 'resetPassword']);
Route::post('/process-reset-password', [ApiAuthController::class, 'processResetPassword']);

Route::post('/save-rating/{productId}',[ApiShopController::class,'saveRating']);


Route::group(['prefix' => 'account'],function() { 
    Route::group(['middleware' => 'guest'],function() {
        Route::get('/login', [ApiAuthController::class, 'login']);
        Route::post('/login', [ApiAuthController::class, 'authenticate']);
        
        Route::get('/register', [ApiAuthController::class, 'register']);
        Route::post('/process-register', [ApiAuthController::class, 'processRegister']);

    });

    Route::group(['middleware' => 'auth'],function() {
        Route::get('/profile', [ApiAuthController::class, 'profile']);
        Route::post('/update-profile', [ApiAuthController::class, 'updateProfile']);
        Route::post('/update-address', [ApiAuthController::class, 'updateAddress']);
        Route::get('/change-password', [ApiAuthController::class, 'showchangePasswordForm']);
        Route::post('/process-change-password', [ApiAuthController::class, 'changePassword']);

        Route::get('/my-orders', [ApiAuthController::class, 'orders']);
        Route::get('/my-wishlist', [ApiAuthController::class, 'wishlist']);
        Route::post('/remove-product-from-wishlist', [ApiAuthController::class, 'removeProductFromWishList']);
        Route::get('/order-detail/{orderId}', [ApiAuthController::class, 'orderDetail']);
        Route::get('/logout', [ApiAuthController::class, 'logout']);

    });
});


Route::group(['prefix' => 'admin'],function() { 

    //Route::group(['middleware' => 'admin.guest'],function() {

        Route::get('/login', [ApiAdminLoginController::class, 'index']);
        Route::post('/login', [ApiAdminLoginController::class, 'login']);
        Route::get('/logout', [ApiAdminLoginController::class, 'logout']);
        
    //});

    //Route::group(['middleware' => 'admin.auth'],function() {
        Route::get('/dashboard', [ApiHomeController::class, 'index']);
        Route::get('/logout', [ApiHomeController::class, 'logout']);

        //Category Routes
        Route::get('/categories', [ApiCategoryController::class, 'index']);
        Route::get('/categories/create', [ApiCategoryController::class, 'create']);
        Route::post('/categories', [ApiCategoryController::class, 'store']);
        Route::get('/categories/{category}/edit', [ApiCategoryController::class, 'edit']);
        Route::put('/categories/{category}', [ApiCategoryController::class, 'update']);
        Route::delete('/categories/{category}', [ApiCategoryController::class, 'destroy']);

        //sub category route
        Route::get('/sub-categories', [ApiSubCategoryController::class, 'index']);
        Route::get('/sub-categories/create', [ApiSubCategoryController::class, 'create']);
        Route::post('/sub-categories', [ApiSubCategoryController::class, 'store']);
        Route::get('/sub-categories/{subCategory}/edit', [ApiSubCategoryController::class, 'edit']);
        Route::post('/sub-categories/{subCategory}', [ApiSubCategoryController::class, 'update']);
        Route::delete('/sub-categories/{subCategory}', [ApiSubCategoryController::class, 'destroy']);

        //brands routes
        Route::get('/brands', [ApiBrandController::class, 'index']);
        Route::get('/brands/create', [ApiBrandController::class, 'create']);
        Route::post('/brands', [ApiBrandController::class, 'store']);
        Route::get('/brands/{brand}/edit', [ApiBrandController::class, 'edit']);
        Route::put('/brands/{brand}', [ApiBrandController::class, 'update']);
        Route::delete('/brands/{brand}', [ApiBrandController::class, 'destroy']);


        //product routes
        Route::get('/products', [ApiProductController::class, 'index']);
        Route::get('/products/create', [ApiProductController::class, 'create']);
        Route::post('/products', [ApiProductController::class, 'store']);
        Route::get('/products/{product}/edit', [ApiProductController::class, 'edit']);
        Route::put('/products/{product}', [ApiProductController::class, 'update']);
        Route::delete('/products/{product}', [ApiProductController::class, 'destroy']);
        Route::get('/get-products', [ApiProductController::class, 'getProducts']);
        Route::get('/ratings', [ApiProductController::class, 'productRatings']);
        Route::get('/change-rating-status', [ApiProductController::class, 'changeRatingStatus']);



        Route::get('/product-subcategories', [ApiProductSubCategoryController::class, 'index']);

        Route::get('/product-images/{id}', [ApiProductImageController::class, 'getImage']);
        Route::post('/product-images/update', [ApiProductImageController::class, 'update']);
        Route::delete('/product-images', [ApiProductImageController::class, 'destroy']);

        //shipping routes
        Route::get('/shipping/create', [ApiShippingController::class, 'create']);
        Route::post('/shipping', [ApiShippingController::class, 'store']);
        Route::get('/shipping/{id}', [ApiShippingController::class, 'edit']);
        Route::put('/shipping/{id}', [ApiShippingController::class, 'update']);
        Route::delete('/shipping/{id}', [ApiShippingController::class, 'destroy']);


        //coupon code routes
        Route::get('/coupons', [ApiDiscountCodeController::class, 'index']);
        Route::get('/coupons/create', [ApiDiscountCodeController::class, 'create']);
        Route::post('/coupons', [ApiDiscountCodeController::class, 'store']);
        Route::get('/coupons/{coupon}/edit', [ApiDiscountCodeController::class, 'edit']);
        Route::put('/coupons/{coupon}', [ApiDiscountCodeController::class, 'update']);
        Route::delete('/coupons/{coupon}', [ApiDiscountCodeController::class, 'destroy']);

        //Order Routes
        Route::get('/orders',[ApiOrderController::class,'index']);
        Route::get('/orders/{id}',[ApiOrderController::class,'detail']);
        Route::post('/orders/change-status/{id}',[ApiOrderController::class,'changeOrderStatus']);
        Route::post('/orders/send-email/{id}',[ApiOrderController::class,'sendInvoiceEmail']);

        //User Routes

        Route::get('/users', [ApiUserController::class, 'index']);
        Route::get('/users/create', [ApiUserController::class, 'create']);
        Route::post('/users', [ApiUserController::class, 'store']);
        Route::get('/users/{user}/edit', [ApiUserController::class, 'edit']);
        Route::put('/users/{user}', [ApiUserController::class, 'update']);
        Route::delete('/users/{user}', [ApiUserController::class, 'destroy']);

        //Page Routes

        Route::get('/pages', [ApiPageController::class, 'index']);
        Route::get('/pages/create', [ApiPageController::class, 'create']);
        Route::post('/pages', [ApiPageController::class, 'store']);
        Route::get('/pages/{page}/edit', [ApiPageController::class, 'edit']);
        Route::put('/pages/{page}', [ApiPageController::class, 'update']);
        Route::delete('/pages/{page}', [ApiPageController::class, 'destroy']);




        //temp-images.create
        Route::post('/upload-temp-image', [TempImagesController::class, 'create']);

        // settings routes
        Route::get('/change-password', [ApiSettingController::class, 'showChangePasswordForm']);
        Route::post('/process-change-password', [ApiSettingController::class, 'processChangePassword']);

        
        Route::get('/getSlug', function(Request $request) {
            $slug = '';
            if(!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        });
    //});


});










// Route:: get('/',[FrontController::class, 'index']);
// Route:: get('/shop',[ShopController::class, 'index']);
// Route::get('/product//{slug}', [ShopController::class,'product']);
// Route::get('/cart', [CartController::class, 'cart']);
// Route::post('/add-to-cart', [CartController::class, 'addToCart']);
// Route::post('/update-cart', [CartController::class, 'updateCart']);
// Route::post('/delete-item', [CartController::class, 'deleteItem']);
// Route::get('/checkout', [CartController::class, 'checkout']);
// Route::get('/process-checkout', [CartController::class, 'processCheckout']);
// Route::get('/thanks/{orderId}', [CartController::class, 'thankyou']);




// Route::group(['prefix' => 'account'],function() { 
//     Route::group(['middleware' => 'guest'],function() {
//         Route::get('/login', [AuthController::class, 'login']);
//         Route::post('/login', [AuthController::class, 'login']);


//         Route::get('/register', [AuthController::class, 'register']);
//         Route::post('/process-register', [AuthController::class, 'processRegister']);

//     });

//     Route::group(['middleware' => 'auth'],function() {
//         Route::get('/profile', [AuthController::class, 'profile']);
//         Route::get('/logout', [AuthController::class, 'logout']);

//     });
// });

// Route::group(['prefix' => 'admin'],function() { 

//     Route::group(['middleware' => 'admin.guest'],function() {

//         Route::get('/login', [AdminLoginController::class, 'index']);
//         Route::post('/authenticate', [AdminLoginController::class, 'authenticate']);

//     });

//     Route::post('/register', [AdminSignupController::class, 'store']);
//     Route::get('/index', [AdminSignupController::class, 'index']);

//     Route::group(['middleware' => 'admin.auth'],function() {
//         Route::get('/dashboard', [HomeController::class, 'index']);
//         Route::get('/logout', [HomeController::class, 'logout']);

//         //Category Routes
//         Route::get('/categories', [CategoryController::class, 'index']);
//         Route::get('/categories/create', [CategoryController::class, 'create']);
//         Route::post('/categories', [CategoryController::class, 'store']);
//         Route::get('/categories/{category}/edit', [CategoryController::class, 'edit']);
//         Route::put('/categories/{category}', [CategoryController::class, 'update']);
//         Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

//         //sub category route
//         Route::get('/sub-categories', [SubCategoryController::class, 'index']);
//         Route::get('/sub-categories/create', [SubCategoryController::class, 'create']);
//         Route::post('/sub-categories', [SubCategoryController::class, 'store']);
//         Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit']);
//         Route::post('/sub-categories/{subCategory}', [SubCategoryController::class, 'update']);
//         Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'destroy']);

//         //brands routes
//         Route::get('/brands', [BrandController::class, 'index']);
//         Route::get('/brands/create', [BrandController::class, 'create']);
//         Route::post('/brands', [BrandController::class, 'store']);
//         Route::get('/brands/{brand}/edit', [BrandController::class, 'edit']);
//         Route::put('/brands/{brand}', [BrandController::class, 'update']);

//         //product routes
//         Route::get('/products', [ProductController::class, 'index']);
//         Route::get('/products/create', [ProductController::class, 'create']);
//         Route::post('/products', [ProductController::class, 'store']);
//         Route::get('/products/{product}/edit', [ProductController::class, 'edit']);
//         Route::put('/products/{product}', [ProductController::class, 'update']);
//         Route::delete('/products/{product}', [ProductController::class, 'destroy']);
//         Route::get('/get-products', [ProductController::class, 'getProducts']);


//         Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index']);

//         Route::post('/product-images/update', [ProductImageController::class, 'update']);
//         Route::delete('/product-images', [ProductImageController::class, 'destroy']);

//         //shipping routes
//         Route::get('/shipping/create', [ShippingController::class, 'create']);
//         Route::post('/shipping', [ShippingController::class, 'store']);
//         Route::get('/shipping/{id}', [ShippingController::class, 'edit']);
//         Route::put('/shipping/{id}', [ShippingController::class, 'update']);
//         Route::delete('/shipping/{id}', [ShippingController::class, 'destroy']);

//         // Route::get('/coupons', [DiscountCodeController::class, 'index']);
//         // Route::get('/coupons/create', [DiscountCodeController::class, 'create']);
//         // Route::post('/coupons', [DiscountCodeController::class, 'store']);
//         // Route::get('/products/{product}/edit', [ProductController::class, 'edit']);
//         // Route::put('/products/{product}', [ProductController::class, 'update']);
//         // Route::delete('/products/{product}', [ProductController::class, 'destroy']);
//         // Route::get('/get-products', [ProductController::class, 'getProducts']);


//         //temp-images.create
//         Route::post('/upload-temp-image', [TempImagesController::class, 'create']);


//         Route::get('/getSlug', function(Request $request) {
//             $slug = '';
//             if(!empty($request->title)) {
//                 $slug = Str::slug($request->title);
//             }
//             return response()->json([
//                 'status' => true,
//                 'slug' => $slug
//             ]);
//         })->name('getSlug');
//     });
// });







// Route::get('/',[ApiFrontController::class, 'index']);
// Route::get('/shop',[ApiShopController::class, 'index']);
// Route::get('/product//{slug}', [ApiShopController::class,'product']);

// Route::post('/admin/login', [ApiAdminLoginController::class, 'authenticate']);
// Route::post('/admin/logout', [ApiAdminLoginController::class, 'logout']);

// Route::post('/register', [ApiAuthController::class, 'register']);
// Route::post('/login', [ApiAuthController::class, 'login']);
// Route::post('/logout', [ApiAuthController::class, 'logout']);

// Route::get('/brands', [ApiBrandController::class, 'index']);
// Route::post('/brands', [ApiBrandController::class, 'store']);
// Route::get('/brands/{id}/edit', [ApiBrandController::class, 'edit']);
// Route::put('/brands/{id}', [ApiBrandController::class, 'update']);

// Route::post('/cart/add', [ApiCartController::class, 'addToCart']);
// Route::get('/cart', [ApiCartController::class, 'cart']);
// Route::put('/cart/update', [ApiCartController::class, 'updateCart']);
// Route::delete('/cart/delete', [ApiCartController::class, 'deleteItem']);

// Route::post('login', [ApiAuthController::class, 'login']);
// Route::post('register', [ApiAuthController::class, 'register']);
// Route::post('register/process', [ApiAuthController::class, 'processRegister']);
// Route::post('authenticate', [ApiAuthController::class, 'authenticate']);
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('profile', [ApiAuthController::class, 'profile']);
//     Route::post('logout', [ApiAuthController::class, 'logout']);
//     Route::get('orders', [ApiAuthController::class, 'orders']);
//     Route::get('orders/{id}', [ApiAuthController::class, 'orderDetail']);
//     Route::get('wishlist', [ApiAuthController::class, 'wishlist']);
//     Route::delete('wishlist/{id}', [ApiAuthController::class, 'removeProductFromWishList']);
// });

// Route::get('/products', [ApiFrontController::class, 'index']);
// Route::post('/wishlist/add', [ApiFrontController::class, 'addToWishList']);

// Route::get('/discount-coupons', [ApiDiscountCodeController::class, 'index']);
// Route::post('/discount-coupons', [ApiDiscountCodeController::class, 'store']);
// Route::get('/discount-coupons/{id}/edit', [ApiDiscountCodeController::class, 'edit']);
// Route::put('/discount-coupons/{id}', [ApiDiscountCodeController::class, 'update']);
// Route::delete('/discount-coupons/{id}', [ApiDiscountCodeController::class, 'destroy']);

// Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ApiShopController::class, 'index'])->name('api.shop.index');
// Route::get('/shop/product/{slug}', [ApiShopController::class, 'product'])->name('api.shop.product');

// Route::post('/cart/add', [ApiCartController::class, 'addToCart']);
// Route::get('/cart', [ApiCartController::class, 'cart']);
// Route::put('/cart/update', [ApiCartController::class, 'updateCart']);
// Route::delete('/cart/delete', [ApiCartController::class, 'deleteItem']);

// // Route for applying discount coupon
// Route::post('/cart/discount/apply', [ApiCartController::class, 'applyDiscount']);

// // Route for removing discount coupon
// Route::delete('/cart/discount/remove', [ApiCartController::class, 'removeCoupon']);


// Route::get('/categories', [ApiCategoryController::class, 'index']);
// Route::get('/categories/create', [ApiCategoryController::class, 'create']);
// Route::post('/categories', [ApiCategoryController::class, 'store']);
// Route::get('/categories/{category}/edit', [ApiCategoryController::class, 'edit']);
// Route::put('/categories/{category}', [ApiCategoryController::class, 'update']);
// Route::delete('/categories/{category}', [ApiCategoryController::class, 'destroy']);


// //product routes
// Route::get('/products', [ApiProductController::class, 'index']);
// Route::get('/products/create', [ApiProductController::class, 'create']);
// Route::post('/products', [ApiProductController::class, 'store']);
// Route::get('/products/{product}/edit', [ApiProductController::class, 'edit']);
// Route::put('/products/{product}', [ApiProductController::class, 'update']);
// Route::delete('/products/{product}', [ApiProductController::class, 'destroy']);
// Route::get('/get-products', [ApiProductController::class, 'getProducts']);


// Route::get('/product-subcategories', [ApiProductSubCategoryController::class, 'index']);

// Route::post('/product-images/update', [ApiProductImageController::class, 'update']);
// Route::delete('/product-images', [ApiProductImageController::class, 'destroy']);


// //shipping routes
// Route::get('/shipping/create', [ApiShippingController::class, 'create']);
// Route::post('/shipping', [ApiShippingController::class, 'store']);
// Route::get('/shipping/{id}', [ApiShippingController::class, 'edit']);
// Route::put('/shipping/{id}', [ApiShippingController::class, 'update']);
// Route::delete('/shipping/{id}', [ApiShippingController::class, 'destroy']);


// //temp images

// Route::post('/upload-temp-image', [TempImagesController::class, 'create']);


//         Route::get('/getSlug', function(Request $request) {
//             $slug = '';
//             if(!empty($request->title)) {
//                 $slug = Str::slug($request->title);
//             }
//             return response()->json([
//                 'status' => true,
//                 'slug' => $slug
//             ]);
//         })->name('getSlug');


