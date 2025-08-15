<?php

use Illuminate\Support\Facades\Route;
use MikiBabi\YagoutPay\Controllers\YagoutController;
use App\Http\Middleware\VerifyCsrfToken;


Route::post('yagoutpay/success', [YagoutController::class, 'success'])->name('yagoutpay.success')->withoutMiddleware([VerifyCsrfToken::class]);
Route::post('yagoutpay/failure', [YagoutController::class, 'failure'])->name('yagoutpay.failure')->withoutMiddleware([VerifyCsrfToken::class]);

