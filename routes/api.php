<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DesignerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::middleware(['cors'])->group(function () {
    // Auth
    Route::post('/login', [ApiController::class, 'authenticate']);
    Route::post('/register', [ApiController::class, 'register']);
    Route::post('/registerpayment', [ApiController::class, 'registerDesigner']);


    Route::group(['middleware' => ['jwt.verify']], function () {

    });
});