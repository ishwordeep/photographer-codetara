<?php

use App\Http\Controllers\API\Admin\CategoryController;
use App\Http\Controllers\API\Admin\ItemSuperTypeController;
use App\Http\Controllers\API\Admin\SubcategoryController;
use App\Http\Controllers\API\Admin\TableController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Middleware\CheckSuperadminOrAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




//Protected API Routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
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
        Route::controller(SubcategoryController::class)->group(function () {
            Route::post('/subcategory', 'store');
            Route::get('/subcategory', 'index');
            Route::get('/subcategory/trash', 'trash');
            Route::get('/subcategory/{id}', 'show');
            Route::post('/subcategory/{id}', 'update');
            Route::delete('/subcategory/{id}', 'destroy');
            Route::post('/subcategory/{id}/restore', 'restore');
        });

        Route::controller(ItemSuperTypeController::class)->group(function () {
            Route::post('/supertype', 'store');
            Route::get('/supertype', 'index');
            Route::get('/supertype/trash', 'trash');
            Route::get('/supertype/{id}', 'show');
            Route::post('/supertype/{id}', 'update');
            Route::delete('/supertype/{id}', 'destroy');
            Route::post('/supertype/{id}/restore', 'restore');
        });
        Route::controller(ItemSuperTypeController::class)->group(function () {
            Route::post('/type', 'store');
            Route::get('/type', 'index');
            Route::get('/type/trash', 'trash');
            Route::get('/type/{id}', 'show');
            Route::post('/type/{id}', 'update');
            Route::delete('/type/{id}', 'destroy');
            Route::post('/type/{id}/restore', 'restore');
        });

        Route::controller(TableController::class)->group(function () {
            Route::post('/table', 'store');
            Route::get('/table', 'index');
            Route::get('/table/trash', 'trash');
            Route::get('/table/{id}', 'show');
            Route::post('/table/{id}', 'update');
            Route::delete('/table/{id}', 'destroy');
            Route::post('/table/{id}/restore', 'restore');
        });
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
