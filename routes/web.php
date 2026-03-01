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

// CLI download — serves the CLI source as a tar.gz or zip
Route::get('/cli/download', function (\Illuminate\Http\Request $request) {
    $cliDir = base_path('cli');
    $format = $request->query('format', 'tar.gz');
    $tempDir = sys_get_temp_dir() . '/apipod-cli-' . uniqid();
    $packageDir = $tempDir . '/apipod-cli';

    mkdir($packageDir, 0755, true);
    mkdir($packageDir . '/src', 0755, true);

    copy($cliDir . '/package.json', $packageDir . '/package.json');
    copy($cliDir . '/src/index.js', $packageDir . '/src/index.js');

    if ($format === 'zip') {
        $archivePath = $tempDir . '/apipod-cli.zip';
        $zip = new \ZipArchive();
        $zip->open($archivePath, \ZipArchive::CREATE);
        $zip->addFile($packageDir . '/package.json', 'apipod-cli/package.json');
        $zip->addFile($packageDir . '/src/index.js', 'apipod-cli/src/index.js');
        $zip->close();
        $contentType = 'application/zip';
        $filename = 'apipod-cli.zip';
    } else {
        $archivePath = $tempDir . '/apipod-cli.tar.gz';
        $cmd = sprintf('tar -czf %s -C %s apipod-cli', escapeshellarg($archivePath), escapeshellarg($tempDir));
        exec($cmd);
        $contentType = 'application/gzip';
        $filename = 'apipod-cli.tar.gz';
    }

    return response()->download($archivePath, $filename, [
        'Content-Type' => $contentType,
    ])->deleteFileAfterSend(true);
})->name('cli.download');

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
    Route::get('/dashboard/quickstart', [DashboardController::class, 'quickstart'])->name('dashboard.quickstart');
    Route::get('/dashboard/models', [DashboardController::class, 'models'])->name('dashboard.models');
    Route::get('/dashboard/usage', [DashboardController::class, 'usage'])->name('dashboard.usage');
    Route::get('/dashboard/api-keys', [DashboardController::class, 'apiKeys'])->name('dashboard.api-keys');
    Route::post('/dashboard/api-keys', [DashboardController::class, 'createApiKey'])->name('dashboard.api-keys.create');
    Route::delete('/dashboard/api-keys/{apiKey}', [DashboardController::class, 'revokeApiKey'])->name('dashboard.api-keys.revoke');
    Route::get('/dashboard/plan', [DashboardController::class, 'planStatus'])->name('dashboard.plan');
    Route::get('/dashboard/provider-keys', [DashboardController::class, 'providerKeys'])->name('dashboard.provider-keys');
    Route::post('/dashboard/provider-keys', [DashboardController::class, 'storeProviderKey'])->name('dashboard.provider-keys.store');
    Route::delete('/dashboard/provider-keys/{providerKey}', [DashboardController::class, 'deleteProviderKey'])->name('dashboard.provider-keys.delete');
    Route::post('/dashboard/select-model', [DashboardController::class, 'selectModel'])->name('dashboard.select-model');
    Route::get('/dashboard/model-weights', [DashboardController::class, 'modelWeights'])->name('dashboard.model-weights');
    Route::post('/dashboard/model-weights', [DashboardController::class, 'updateModelWeights'])->name('dashboard.model-weights.update');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');

    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Shop routes
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::match(['get', 'post'], '/shop/{plan}/purchase', [ShopController::class, 'purchase'])->name('shop.purchase');
    Route::get('/shop/success/{payment}', [ShopController::class, 'success'])->name('shop.success');
    Route::get('/shop/failed/{payment}', [ShopController::class, 'failed'])->name('shop.failed');
});

// Midtrans webhook (public route)
Route::post('/webhooks/midtrans', [PaymentController::class, 'webhook'])->name('webhooks.midtrans');
