<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RolesController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\BorrowController;

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

Route::prefix('v1')->group(function(){

        // ======== Public Route ========

        // Category
        Route::get('category', [CategoryController::class, 'index']);
        Route::get('category/{id}', [CategoryController::class, 'show']);

        // Books
        Route::get('book', [BookController::class, 'index']);
        Route::get('book/{id}', [BookController::class, 'show']);


         // Hanya user yang sudah login bisa mengakses rute ini
        Route::middleware(['auth:api'])->group(function (){
            Route::post('profile', [ProfileController::class, 'store']);
            Route::put('profile/{id}', [ProfileController::class, 'update']);

            Route::post('barrow', [BorrowController::class, 'store']);
            Route::put('barrow/{id}', [BorrowController::class, 'update']);
        });

         // Hanya admin dan owner yang bisa mengakses rute ini
         Route::middleware(['auth:api', 'isAdmin'])->group(function ()
         {
            // Roles
            Route::apiResource('role', RolesController::class);

            // Category
            Route::post('category', [CategoryController::class, 'store']);
            Route::put('category/{id}', [CategoryController::class, 'update']);
            Route::delete('category/{id}', [CategoryController::class, 'destroy']);

            // Book
            Route::post('book', [BookController::class, 'store']);
            Route::post('book/{id}', [BookController::class, 'update']);
            Route::delete('book/{id}', [BookController::class, 'destroy']);

            // Borrow
            Route::get('barrow', [BorrowController::class, 'index']);
         });


         Route::prefix('auth')->group(function (){
            Route::post('register', [AuthController::class, 'register']);
            Route::post('login', [AuthController::class, 'login']);
            Route::get('me', [AuthController::class, 'getUser'])->middleware('auth:api');
            Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
        });


    });
