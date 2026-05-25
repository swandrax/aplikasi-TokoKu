<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ReceiptController;

// 1. PUBLIC ROUTES
Route::get('/', [LoginController::class, 'landingPage'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,1');

    Route::get('/otp/verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('/otp/verify', [OtpController::class, 'verify']);
    Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Receipt Printing (Broad access under auth)
Route::middleware('auth')->group(function () {
    Route::get('/struk/{order}/pdf', [ReceiptController::class, 'downloadPdf'])->name('struk.pdf');
    Route::get('/struk/{order}/print', [ReceiptController::class, 'printHtml'])->name('struk.print');
});

// 2. ADMIN ROUTES
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Resource routes
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class, ['names' => 'admin.categories']);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class, ['names' => 'admin.products']);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class, ['names' => 'admin.users']);
    
    // FIFO Stock Batch Management
    Route::get('/stock', [\App\Http\Controllers\Admin\StockController::class, 'index'])->name('admin.stock.index');
    Route::get('/stock/create', [\App\Http\Controllers\Admin\StockController::class, 'create'])->name('admin.stock.create');
    Route::post('/stock', [\App\Http\Controllers\Admin\StockController::class, 'store'])->name('admin.stock.store');
    
    // Report export
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('admin.reports.excel');
    Route::get('/reports/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('admin.reports.pdf');
    
    // Logs
    Route::get('/logs', [\App\Http\Controllers\Admin\DashboardController::class, 'logs'])->name('admin.logs');
});

// 3. KASIR ROUTES
Route::prefix('kasir')->middleware(['auth', 'kasir'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Kasir\DashboardController::class, 'index'])->name('kasir.dashboard');
    
    // Point of Sale (POS)
    Route::get('/pos', [\App\Http\Controllers\Kasir\PosController::class, 'index'])->name('kasir.pos.index');
    Route::post('/pos/checkout', [\App\Http\Controllers\Kasir\PosController::class, 'checkout'])->name('kasir.pos.checkout');
    
    // Transaction history
    Route::get('/transactions', [\App\Http\Controllers\Kasir\TransactionController::class, 'index'])->name('kasir.transactions.index');
});

// 4. PEMBELI ROUTES
Route::prefix('toko')->middleware(['auth', 'pembeli'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Pembeli\ShopController::class, 'index'])->name('pembeli.shop.index');
    Route::get('/product/{product}', [\App\Http\Controllers\Pembeli\ShopController::class, 'show'])->name('pembeli.shop.show');
    
    // Shopping Cart
    Route::get('/cart', [\App\Http\Controllers\Pembeli\CartController::class, 'index'])->name('pembeli.cart.index');
    Route::post('/cart/add', [\App\Http\Controllers\Pembeli\CartController::class, 'add'])->name('pembeli.cart.add');
    Route::post('/cart/update', [\App\Http\Controllers\Pembeli\CartController::class, 'update'])->name('pembeli.cart.update');
    Route::post('/cart/remove', [\App\Http\Controllers\Pembeli\CartController::class, 'remove'])->name('pembeli.cart.remove');
    
    // Checkout
    Route::get('/checkout', [\App\Http\Controllers\Pembeli\OrderController::class, 'checkout'])->name('pembeli.order.checkout');
    Route::post('/checkout', [\App\Http\Controllers\Pembeli\OrderController::class, 'store'])->name('pembeli.order.store');
    
    // Order History & Tracking
    Route::get('/orders', [\App\Http\Controllers\Pembeli\OrderController::class, 'index'])->name('pembeli.order.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Pembeli\OrderController::class, 'show'])->name('pembeli.order.show');
    
    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\Pembeli\ProfileController::class, 'index'])->name('pembeli.profile.index');
    Route::post('/profile', [\App\Http\Controllers\Pembeli\ProfileController::class, 'update'])->name('pembeli.profile.update');
});

// 5. INTERNAL REAL-TIME POLLING API
Route::prefix('api/internal')->middleware('auth')->group(function () {
    Route::get('/dashboard-stats', [\App\Http\Controllers\Api\InternalApiController::class, 'dashboardStats'])->name('api.internal.dashboard_stats');
    Route::get('/stock-alerts', [\App\Http\Controllers\Api\InternalApiController::class, 'stockAlerts'])->name('api.internal.stock_alerts');
    Route::get('/order-status/{order}', [\App\Http\Controllers\Api\InternalApiController::class, 'orderStatus'])->name('api.internal.order_status');
    Route::get('/notifications', [\App\Http\Controllers\Api\InternalApiController::class, 'notifications'])->name('api.internal.notifications');
    Route::get('/cart-count', [\App\Http\Controllers\Api\InternalApiController::class, 'cartCount'])->name('api.internal.cart_count');
    Route::get('/pos-stock/{product}', [\App\Http\Controllers\Api\InternalApiController::class, 'posStock'])->name('api.internal.pos_stock');
});

// 6. CHATBOT API (Protected with CSRF + Rate Limiting)
Route::middleware(['web', 'throttle:30,1'])->group(function () {
    Route::post('/api/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('api.chatbot.send');
    Route::post('/api/chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('api.chatbot.clear');
    Route::post('/api/chatbot/score', [ChatbotController::class, 'scoreResponse'])->name('api.chatbot.score');
});

// 7. LOCALIZATION
Route::get('/lang/{lang}', [\App\Http\Controllers\LanguageController::class, 'switchLang'])->name('lang.switch');

// 8. TEST ROUTES (Only available in debug mode)
if (config('app.debug')) {
    Route::get('/ping-500', function() {
        abort(500, 'This is a test 500 error.');
    });
}
