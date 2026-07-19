<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Production\DashboardController as ProductionDashboardController;
use App\Http\Controllers\Production\OrderController as ProductionOrderController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ReportController as OwnerReportController;
use App\Http\Controllers\Owner\InvoiceController as OwnerInvoiceController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Customer Public Routes
Route::prefix('customer')->name('customer.')->group(function () {
    // Products
    Route::get('/products', [CustomerProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [CustomerProductController::class, 'show'])->name('products.show');
});

// Customer Routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Orders - Katalog
    Route::get('/orders/catalog/{product}', [CustomerOrderController::class, 'createCatalog'])->name('orders.catalog.create');
    Route::post('/orders/catalog/{product}', [CustomerOrderController::class, 'storeCatalog'])->name('orders.catalog.store');

    // Orders - Custom
    Route::get('/orders/custom', [CustomerOrderController::class, 'createCustom'])->name('orders.custom.create');
    Route::post('/orders/custom', [CustomerOrderController::class, 'storeCustom'])->name('orders.custom.store');

    // Orders - List & Detail
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');

    // Payments
    Route::get('/orders/{order}/payment', [CustomerPaymentController::class, 'create'])->name('payments.create');
    Route::post('/orders/{order}/payment', [CustomerPaymentController::class, 'store'])->name('payments.store');

    // Chat
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/conversation', [CustomerChatController::class, 'getOrCreateConversation'])->name('conversation');
        Route::get('/conversations/{conversation}/messages', [CustomerChatController::class, 'getMessages'])->name('messages');
        Route::get('/conversations/{conversation}/poll', [CustomerChatController::class, 'pollMessages'])->name('poll');
        Route::post('/conversations/{conversation}/messages', [CustomerChatController::class, 'sendMessage'])->name('send');
        Route::get('/unread', [CustomerChatController::class, 'getUnreadCount'])->name('unread');
        Route::post('/push-subscribe', [CustomerChatController::class, 'pushSubscribe'])->name('push.subscribe');
        Route::get('/vapid-public-key', [CustomerChatController::class, 'vapidPublicKey'])->name('vapid.key');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/price', [AdminOrderController::class, 'updatePrice'])->name('orders.update-price');
    Route::post('/orders/{order}/send-to-production', [AdminOrderController::class, 'sendToProduction'])->name('orders.send-to-production');

    // Payment Management
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');

    // Products Management
    Route::resource('products', AdminProductController::class);
    
    // Invoices
    Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{order}', [AdminInvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{order}/pdf', [AdminInvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::get('/invoices/{order}/print', [AdminInvoiceController::class, 'print'])->name('invoices.print');

    // Chat Management
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [AdminChatController::class, 'index'])->name('index');
        Route::get('/conversations/poll', [AdminChatController::class, 'pollConversations'])->name('conversations.poll');
        Route::get('/conversations/{conversation}', [AdminChatController::class, 'show'])->name('show');
        Route::get('/conversations/{conversation}/poll', [AdminChatController::class, 'pollMessages'])->name('poll');
        Route::post('/conversations/{conversation}/messages', [AdminChatController::class, 'sendMessage'])->name('send');
        Route::post('/conversations/{conversation}/takeover', [AdminChatController::class, 'takeover'])->name('takeover');
        Route::post('/conversations/{conversation}/handback', [AdminChatController::class, 'handback'])->name('handback');
        Route::post('/push-subscribe', [AdminChatController::class, 'pushSubscribe'])->name('push.subscribe');
        Route::get('/vapid-public-key', [AdminChatController::class, 'vapidPublicKey'])->name('vapid.key');

        // Knowledge Base
        Route::get('/knowledge-base', [AdminChatController::class, 'knowledgeBase'])->name('knowledge-base');
        Route::post('/knowledge-base', [AdminChatController::class, 'storeKnowledge'])->name('knowledge-base.store');
        Route::put('/knowledge-base/{knowledge}', [AdminChatController::class, 'updateKnowledge'])->name('knowledge-base.update');
        Route::delete('/knowledge-base/{knowledge}', [AdminChatController::class, 'destroyKnowledge'])->name('knowledge-base.destroy');
        Route::patch('/knowledge-base/{knowledge}/toggle', [AdminChatController::class, 'toggleKnowledge'])->name('knowledge-base.toggle');
        Route::post('/settings/threshold', [AdminChatController::class, 'updateThreshold'])->name('settings.threshold');
    });
});

// Production Routes
Route::middleware(['auth', 'role:production'])->prefix('production')->name('production.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ProductionDashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders', [ProductionOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [ProductionOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/update-progress', [ProductionOrderController::class, 'updateProgress'])->name('orders.update-progress');
});

// Owner Routes
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    
    // Financial Reports
    Route::get('/reports/financial', [OwnerReportController::class, 'financial'])->name('reports.financial');
    Route::get('/reports/export-excel', [OwnerReportController::class, 'exportExcel'])->name('reports.export-excel');
    
    // Invoices
    Route::get('/invoices', [OwnerInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{order}', [OwnerInvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{order}/pdf', [OwnerInvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::get('/invoices/{order}/print', [OwnerInvoiceController::class, 'print'])->name('invoices.print');
});
