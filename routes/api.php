<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Fees\FeesController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\GroupController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\SocialLoginController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\User\BusinessRequestController;

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


Route::group(['prefix' => 'v2'], function () {
        
   //Authentication Route
    Route::post('/admin/admin-register', [AuthController::class, 'registerAdmin']);
   //  Route::post('/loginAdmin', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    // Route::get('/email/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::get('/email/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify')->middleware(['signed',]);

    Route::post('password/forgot-password', [NewPasswordController::class, 'forgotPassword']);
    Route::post('password/reset', [NewPasswordController::class, 'reset']);


   // social login impimentation
    Route::get('login/{driver}', [SocialLoginController::class, 'redirectToProvider']);
    Route::get('login/{driver}/callback', [SocialLoginController::class, 'handleProviderCallback']);

    
    //////////////////////////////--------Payment Routes-------///////////////////////////////////////////
  // Route::post('/paystack-payment', [FeesController::class, 'payStack']);
    Route::post('/paystack-webhook', [FeesController::class, 'payStackWebhook']);


        //////////////////////ALL ROUTE ACCESSIBLE TO ALL USER //////////////// 
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::get('/categories/{category}/products', [ProductController::class, 'showByCategory']);  //get all products by category
        Route::get('/categories', [CategoryController::class, 'index']);
       
  
});


Route::group(  ['middleware'=> ['auth:sanctum','verified'],  'prefix' => 'v2' ], function () {  
    Route::post('/logout', [AuthController::class, 'logout']);

          // Manage User routes
    Route::get('/admin/users', [AdminController::class, 'index']);
    Route::get('/admin/users/{id}', [AdminController::class, 'show']);
     Route::post('admin/users', [AdminController::class, 'store']);
     Route::patch('admin/users', [AdminController::class, 'update']);
     Route::patch('/admin/users/make-admin/{id}', [AdminController::class, 'makeAdmin']);
     Route::patch('/admin/users/make-user/{id}', [AdminController::class, 'makeUser']);
     Route::delete('/admin/users/{id}', [AdminController::class, 'delete']);
     
   // Vendor only route
    Route::get('/users/{id}', [UserController::class, 'showVendor']);
    Route::patch('/users/{id}', [UserController::class, 'updateUser']);
    Route::patch('/join-group/{id}', [UserController::class, 'joinGroup']);
    Route::patch('/leave-group/{id}', [UserController::class, 'leaveGroup']);
    

    //  profile route
    Route::get('/profiles', [ProfileController::class, 'index']);
    Route::get('/profiles/{id}', [ProfileController::class, 'show']);
    Route::get('/user-profile/{id}', [ProfileController::class, 'userProfile']);
    Route::post('profiles', [ProfileController::class, 'store']);
    Route::patch('/profiles/{id}', [ProfileController::class, 'update']);
    Route::delete('/profiles/{id}', [ProfileController::class, 'delete']);

  //  category route
    // Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::patch('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'delete']);
    
     // product route
    // Route::get('/products', [ProductController::class, 'index']);
    // Route::get('/cat-products/{id}', [ProductController::class, 'showBycategory']);
    Route::get('/user-product/{id}', [ProductController::class, 'showUserProduct']);
    Route::post('products', [ProductController::class, 'store']);
    Route::patch('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'delete']);


  Route::get('/groups', [GroupController::class, 'index']);
  Route::get('/groups/{id}', [GroupController::class, 'show']);
  Route::get('/user-group/{id}', [GroupController::class, 'showUserGroup']);
  Route::post('/business-group', [GroupController::class, 'store']);
  Route::patch('/groups/{id}', [GroupController::class, 'update']);
Route::delete('/groups/{id}', [GroupController::class, 'delete']);

  //for group request
  Route::get('/group-request/{id}', [GroupController::class, 'groupRequest']);
 

   Route::get('/requests', [BusinessRequestController::class, 'index']);
    Route::get('/requests/{id}', [BusinessRequestController::class, 'show']);
    Route::get('/user-requests/{id}', [BusinessRequestController::class, 'showUserRequest']);
    Route::post('requests', [BusinessRequestController::class, 'store']);
    Route::post('/requests/{id}', [BusinessRequestController::class, 'update']);
    Route::delete('/requests/{id}', [BusinessRequestController::class, 'delete']);

/////////////////////////MANAGE PAYMENT/////////////////////
Route::get('/payments', [FeesController::class, 'index']);
Route::post('/paystack-payment', [FeesController::class, 'payStack']);
// Route::post('/paystack-webhook', [FeesController::class, 'payStackWebhook']);

Route::post('/filter-payment', [FeesController::class, 'filter']);

     
});
      


//  Route::group(  ['middleware'=> ['auth:sanctum'],  'prefix' => 'v1' ], function () {  
//     Route::post('/logout', [AuthController::class, 'logout']);
   
//  });
      

 
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum','verified'); 

// Route::middleware('auth:sanctum','verified')->get('/user', function (Request $request) {
//     return $request->user();
// });
