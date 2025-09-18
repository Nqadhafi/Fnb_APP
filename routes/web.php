<?php

use Illuminate\Support\Facades\Route;

// PUBLIC & AUTH CONTROLLERS
use App\Http\Controllers\{
    HomeController,
    AuthController,
    MenuController,
    CartController,
    CheckoutController,
    OrderController as UserOrderController,
    PaymentController
};

// ADMIN CONTROLLERS
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    CategoryController as AdminCategoryController,
    ProductController as AdminProductController,
    DiningTableController as AdminDiningTableController,
    TableSessionController as AdminTableSessionController,
    OrderController as AdminOrderController,
    PosSessionController as AdminPosSessionController,
    ReportController as AdminReportController
};

/*
|--------------------------------------------------------------------------
| PUBLIC (no auth required)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// Menu & Produk
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{product:slug}', [MenuController::class, 'show'])->name('menu.show');

/*
|--------------------------------------------------------------------------
| AUTH (Bootstrap views)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // Forgot / Reset Password
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendForgot'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'doReset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| CUSTOMER AREA (auth required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/item/{item}/update', [CartController::class, 'update'])->name('cart.item.update');
    Route::delete('/cart/item/{item}', [CartController::class, 'remove'])->name('cart.item.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/set-table',   [CartController::class, 'setTable'])->name('cart.setTable');
    Route::post('/cart/claim-table', [CartController::class, 'claimTable'])->name('cart.claimTable');


    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');


    // Orders (riwayat & detail user)
    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');

    // Upload bukti pembayaran (transfer/e-wallet)
    Route::post('/orders/{order}/payments/upload-proof', [PaymentController::class, 'uploadProof'])
        ->name('orders.payments.upload');
});

/*
|--------------------------------------------------------------------------
| ADMIN AREA (AdminLTE) — auth + admin middleware
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth','admin'])->group(function () {

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Categories (CRUD)
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Products (CRUD + gallery)
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // Product gallery actions
    Route::delete('/products/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::post('/products/{product}/images/{image}/set-primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.images.primary');

    // Dining Tables (CRUD)
    Route::get('/tables', [AdminDiningTableController::class, 'index'])->name('tables.index');
    Route::get('/tables/create', [AdminDiningTableController::class, 'create'])->name('tables.create');
    Route::post('/tables', [AdminDiningTableController::class, 'store'])->name('tables.store');
    Route::get('/tables/{table}/edit', [AdminDiningTableController::class, 'edit'])->name('tables.edit');
    Route::put('/tables/{table}', [AdminDiningTableController::class, 'update'])->name('tables.update');
    Route::delete('/tables/{table}', [AdminDiningTableController::class, 'destroy'])->name('tables.destroy');

    // Table Sessions (open/close)
    Route::post('/tables/{table}/sessions/open', [AdminTableSessionController::class, 'open'])->name('tables.sessions.open');
    Route::post('/table-sessions/{session}/close', [AdminTableSessionController::class, 'close'])->name('tables.sessions.close');

    // Orders (POS)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status.update');

    // Order Item status (dapur/bar)
    Route::post('/order-items/{item}/prep-status', [AdminOrderController::class, 'updateItemStatus'])->name('orders.items.prepstatus');

    // POS: pembayaran tunai & verifikasi transfer/e-wallet
    Route::post('/orders/{order}/pay-cash', [AdminOrderController::class, 'payCash'])->name('orders.pay.cash');
    Route::post('/payments/{payment}/verify', [AdminOrderController::class, 'verifyPayment'])->name('payments.verify');

    // Cetak struk
    Route::get('/orders/{order}/receipt', [AdminOrderController::class, 'printReceipt'])->name('orders.receipt');

    // POS Sessions (open/close)
    Route::get('/pos-sessions', [AdminPosSessionController::class, 'index'])->name('pos.index');
    Route::post('/pos-sessions/open', [AdminPosSessionController::class, 'open'])->name('pos.open');
    Route::post('/pos-sessions/{session}/close', [AdminPosSessionController::class, 'close'])->name('pos.close');

    // Reports
    Route::get('/reports/sales', [AdminReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/sales/print', [AdminReportController::class, 'salesPrint'])->name('reports.sales.print');
});
