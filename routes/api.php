<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\AdminSignupController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
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


Route:: get('/',[FrontController::class, 'index']);
Route:: get('/shop',[ShopController::class, 'index']);
Route::get('/product//{slug}', [ShopController::class,'product']);

Route::group(['prefix' => 'admin'],function() { 

    Route::group(['middleware' => 'admin.guest'],function() {

        Route::get('/login', [AdminLoginController::class, 'index']);
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate']);

    });

    Route::post('/register', [AdminSignupController::class, 'store']);
    Route::get('/index', [AdminSignupController::class, 'index']);

    Route::group(['middleware' => 'admin.auth'],function() {
        Route::get('/dashboard', [HomeController::class, 'index']);
        Route::get('/logout', [HomeController::class, 'logout']);

        //Category Routes
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/create', [CategoryController::class, 'create']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        //sub category route
        Route::get('/sub-categories', [SubCategoryController::class, 'index']);
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create']);
        Route::post('/sub-categories', [SubCategoryController::class, 'store']);
        Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit']);
        Route::post('/sub-categories/{subCategory}', [SubCategoryController::class, 'update']);
        Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'destroy']);

        //brands routes
        Route::get('/brands', [BrandController::class, 'index']);
        Route::get('/brands/create', [BrandController::class, 'create']);
        Route::post('/brands', [BrandController::class, 'store']);
        Route::get('/brands/{brand}/edit', [BrandController::class, 'edit']);
        Route::put('/brands/{brand}', [BrandController::class, 'update']);

        //product routes
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/create', [ProductController::class, 'create']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::get('/products/{product}/edit', [ProductController::class, 'edit']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        Route::get('/get-products', [ProductController::class, 'getProducts']);


        Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index']);

        Route::post('/product-images/update', [ProductImageController::class, 'update']);
        Route::delete('/product-images', [ProductImageController::class, 'destroy']);



        //temp-images.create
        Route::post('/upload-temp-image', [TempImagesController::class, 'create']);


        Route::get('/getSlug', function(Request $request) {
            $slug = '';
            if(!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('getSlug');
    });


});