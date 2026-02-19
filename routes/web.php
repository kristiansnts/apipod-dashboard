<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\DeviceAuthController;
use App\Services\TokenUsageService;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('home');
})->middleware('auth')->name('home');

use Laravel\Socialite\Facades\Socialite;

// Device auth for CLI login (must be before {provider} wildcard)
Route::get('/auth/device', [DeviceAuthController::class, 'show'])->name('device.show');
Route::get('/auth/device/success', fn () => view('auth.device-success'))->name('device.success');
Route::post('/auth/device/authorize', [DeviceAuthController::class, 'approveDevice'])->middleware('auth')->name('device.authorize');

Route::get('/auth/{provider}', function ($provider) {
    return Socialite::driver($provider)->redirect();
})->name('social.redirect');

Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialiteController::class, 'handleProviderCallback'])->name('social.callback');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Shop routes
    Route::get('/shop', [App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
    Route::post('/shop/{plan}/purchase', [App\Http\Controllers\ShopController::class, 'purchase'])->name('shop.purchase');
    Route::get('/shop/success/{payment}', [App\Http\Controllers\ShopController::class, 'success'])->name('shop.success');
    Route::get('/shop/failed/{payment}', [App\Http\Controllers\ShopController::class, 'failed'])->name('shop.failed');
});

// Xendit webhook (public route)
Route::post('/webhooks/xendit', [App\Http\Controllers\PaymentController::class, 'webhook'])->name('webhooks.xendit');
