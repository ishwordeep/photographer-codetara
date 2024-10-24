<?php

use App\Http\Controllers\API\Admin\AvailabilityController;
use App\Http\Controllers\API\Admin\BookingController;
use App\Http\Controllers\API\Admin\CategoryController;
use App\Http\Controllers\API\Admin\MessageController;
use App\Http\Controllers\API\Admin\PhotographerController;
use App\Http\Controllers\API\Admin\SubcategoryController;
use App\Http\Controllers\API\Admin\SwitchActiveStatusController;
use App\Http\Controllers\API\Admin\WorkController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Middleware\CheckSuperadmin;
use App\Http\Middleware\CheckSuperadminOrAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




//Protected API Routes
// Route::middleware(['auth:sanctum', 'verified'])->group(function () {
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/user', 'user');
        Route::post('/profile-update', 'profileUpdate');
        Route::post('/change-password',  'changePassword');
        Route::post('update-profile-picture', 'updateProfilePicture');
    });

    Route::get('/auth-test', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Protected API is working fine'
        ]);
    });

    Route::middleware([CheckSuperadmin::class])->group(function () {
        Route::get('users-list', [ProfileController::class, 'usersList']);
    });


    // Super Admin & Admin Accessible Routes 
    Route::middleware([CheckSuperadminOrAdmin::class])->group(function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::post('/category', 'store');
            Route::get('/category', 'index');
            Route::get('/category/trash', 'trash');
            Route::get('/category/get-list', 'getCategoryList');
            Route::get('/category/{id}', 'show');
            Route::post('/category/{id}', 'update');
            Route::delete('/category/{id}', 'destroy');
            Route::post('/category/{id}/restore', 'restore');
        });

        Route::controller(WorkController::class)->group(function () {
            Route::post('/work', 'store');
            Route::get('/work', 'index');
            // Route::get('/work/trash', 'trash');
            Route::get('/work/{id}', 'show');
            Route::post('/work/{id}', 'update');
            Route::delete('/work/{id}', 'destroy');
            // Route::post('/work/{id}/restore', 'restore');
        });


        Route::controller(PhotographerController::class)->group(function () {
            // Route::post('/photographer', 'store');
            // Route::get('/photographer', 'index');
            Route::get('/photographer/{id}', 'show');
            Route::post('/photographer/{id}', 'update');
            // Route::delete('/photographer/{id}', 'destroy');
        });

        Route::controller(MessageController::class)->group(function () {
            Route::get('/message', 'index');
            Route::get('/message/{id}', 'show');
            Route::delete('/message/{id}', 'destroy');
        });

        Route::controller(AvailabilityController::class)->group(function () {
            Route::get('/availability', 'index');
            Route::post('/availability', 'store');
            Route::delete('/availability/{id}', 'destroy');
        });

        // booking
        Route::controller(BookingController::class)->group(function () {
            Route::get('/booking', 'index');
            Route::get('/booking/{id}', 'show');
            Route::post('/booking/{id}', 'update');
            Route::delete('/booking/{id}', 'destroy');
        });


        Route::post('/toggle-status/{modelName}/{id}', [SwitchActiveStatusController::class, 'toggleStatus']);
    });
});





//Public API Routes

Route::get('/test', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'API is working fine'
    ]);
});



require __DIR__ . '/auth.php';
require __DIR__ . '/frontend.php';
