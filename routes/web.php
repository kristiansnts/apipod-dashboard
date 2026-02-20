<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\DeviceAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return view('welcome');
})->name('landing');

// Device auth for CLI login (must be before {provider} wildcard)
Route::get('/auth/device', [DeviceAuthController::class, 'show'])->name('device.show');
Route::get('/auth/device/success', fn() => view('auth.device-success'))->name('device.success');
Route::post('/auth/device/authorize', [DeviceAuthController::class, 'approveDevice'])->middleware('auth')->name('device.authorize');

Route::get('/auth/{provider}', function ($provider) {
    return Socialite::driver($provider)->redirect();
})->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'handleProviderCallback'])->name('social.callback');

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
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard/models', [DashboardController::class, 'models'])->name('dashboard.models');
    Route::get('/dashboard/usage', [DashboardController::class, 'usage'])->name('dashboard.usage');
    Route::get('/dashboard/api-keys', [DashboardController::class, 'apiKeys'])->name('dashboard.api-keys');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');

    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Shop routes
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::match(['get', 'post'], '/shop/{plan}/purchase', [ShopController::class, 'purchase'])->name('shop.purchase');
    Route::get('/shop/success/{payment}', [ShopController::class, 'success'])->name('shop.success');
    Route::get('/shop/failed/{payment}', [ShopController::class, 'failed'])->name('shop.failed');
});

// Xendit webhook (public route)
Route::post('/webhooks/xendit', [PaymentController::class, 'webhook'])->name('webhooks.xendit');
