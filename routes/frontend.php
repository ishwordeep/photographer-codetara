<?php





// for frontend data create routes with prefix with group

use App\Http\Controllers\API\Frontend\AvailabilityController;
use App\Http\Controllers\API\Frontend\BookingController;
use App\Http\Controllers\API\Frontend\CategoryController;
use App\Http\Controllers\API\Frontend\MessageController;
use App\Http\Controllers\API\Frontend\PhotographerController;
use App\Http\Controllers\API\Frontend\WorkController;
use Illuminate\Support\Facades\Route;

Route::prefix('frontend')->group(function () {
    Route::controller(PhotographerController::class)->group(function () {
        Route::get('/photographer', 'index');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/category', 'index');
        Route::get('/category/{id}', 'show');
        Route::get('/get-category-list', 'getCategoryList');
    });
    Route::controller(WorkController::class)->group(function () {
        Route::get('/work', 'index');
        Route::get('/work/{id}', 'show');
    });

    Route::controller(MessageController::class)->group(function () {
        Route::post('/message', 'store');
    });

    // Route::controller(SubcategoryController::class)->group(function () {
    //     Route::get('/subcategory', 'index');
    //     Route::get('/subcategory/{id}', 'show');
    // });



    Route::controller(AvailabilityController::class)->group(function () {
        Route::get('/availability', 'index');
    });

    Route::controller(BookingController::class)->group(function () {
        Route::post('/booking', 'store');
    });
});
