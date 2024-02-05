<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 

use App\Http\Controllers\Api\AuthController;  
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrdersController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::prefix('v1')->group(function() {

    Route::get('/test', function(){
        return response()->json(['success' => true,'msg' => 'TEST'], 200);
    });

    //Auth.
    Route::controller(AuthController::class)->group(function() {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/phoneVerifyCodeSendAgain', 'phoneVerifyCodeSendAgain');
        Route::post('/phoneVerify', 'phoneVerify');
    });


    //Just Logined used. 
    Route::middleware('auth:sanctum')->group(function() { 
        
        Route::controller(ProductsController::class)->group(function() { 
            Route::post('/addProduct', 'addProduct');
            Route::post('/products', 'products');
        });

        Route::controller(CartController::class)->group(function() {
            Route::get('/getCart', 'getCart');
            Route::post('/addCart', 'addCart'); 
            Route::post('/removeCart', 'removeCart');
        });
        
        Route::controller(OrdersController::class)->group(function() {
            Route::post('/createOrder', 'createOrder');
            Route::get('/getOrders', 'getOrders');
        });

        Route::controller(ProductsController::class)->group(function() {
            Route::get('/getCategories', 'getCategories');
            Route::get('/getSubCategories', 'getSubCategories');
    
        });

    });
   

});
 

//invalid url..
Route::fallback(function(){
    return response()->json(['success' => false,'msg' => 'Root Not Found'], 404);
});
