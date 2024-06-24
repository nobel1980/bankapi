<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\online_payments;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\NagadController;
use App\Http\Controllers\API\PolicyController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::apiResource('online_payments', PolicyController::class)->middleware('auth:api');

Route::middleware('auth:api')->group( function(){
        
    
});

Route::post('/policy/policy-info', [PolicyController::class, 'info']);

//Router for Nagad Payment
Route::post('/policy/payment', [PolicyController::class, 'payment']);

//Router for Rocket Payment
 Route::post('/policy/payment-rocket', [PolicyController::class, 'payment_rocket']);

 //Router for Exim Bank Premium Payment
 Route::post('/policy/payment-eximbank', [PolicyController::class, 'payment_eximbank']);


//check for payment status
Route::get('/policy/payment-info/{payment_ref_id}', [PolicyController::class, 'status']);