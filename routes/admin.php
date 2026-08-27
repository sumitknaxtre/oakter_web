<?php

use App\Http\Controllers\Admin\AbandonedOrderController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsArticleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ShopifyCustomerController;
use App\Http\Controllers\Admin\ShopifyOrderController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Middleware\RedirectIfAdminAuthenticated;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(RedirectIfAdminAuthenticated::class)->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'staff'])->group(function () {
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::middleware('permission:dashboard')->group(function () {
            Route::get('/', DashboardController::class)->name('dashboard');
        });

        Route::middleware('permission:orders')->group(function () {
            Route::get('orders/export-payment-ids', [OrderController::class, 'exportPaymentIds'])->name('orders.payment-ids.export');
            Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}/customer/edit', [OrderController::class, 'editCustomer'])->name('orders.customer.edit');
            Route::put('orders/{order}/customer', [OrderController::class, 'updateCustomer'])->name('orders.customer.update');
            Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
            Route::post('orders/{order}/unicommerce-resync', [OrderController::class, 'resyncUnicommerce'])->name('orders.unicommerce.resync');
            Route::post('orders/{order}/shiprocket-resync', [OrderController::class, 'resyncShiprocket'])->name('orders.shiprocket.resync');
            Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::get('abandoned-orders/export', [AbandonedOrderController::class, 'export'])->name('abandoned-orders.export');
            Route::get('abandoned-orders', [AbandonedOrderController::class, 'index'])->name('abandoned-orders.index');
            Route::post('abandoned-orders/{order}/confirm-payment', [AbandonedOrderController::class, 'confirmPayment'])->name('abandoned-orders.confirm-payment');

            Route::get('shopify-orders', [ShopifyOrderController::class, 'index'])->name('shopify-orders.index');
            Route::get('shopify-orders/{shopifyOrder}', [ShopifyOrderController::class, 'show'])->name('shopify-orders.show');
        });

        Route::middleware('permission:customers')->group(function () {
            Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');

            Route::get('shopify-customers', [ShopifyCustomerController::class, 'index'])->name('shopify-customers.index');
            Route::get('shopify-customers/{shopifyCustomer}', [ShopifyCustomerController::class, 'show'])->name('shopify-customers.show');
        });

        Route::middleware('permission:products')->group(function () {
            Route::get('products', [ProductController::class, 'index'])->name('products.index');
            Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        });

        Route::middleware('permission:coupons')->group(function () {
            Route::resource('coupons', CouponController::class)->except(['show']);
        });

        Route::middleware('permission:news')->group(function () {
            Route::resource('news-articles', NewsArticleController::class)->except(['show']);
        });

        Route::middleware('admin')->group(function () {
            Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
            Route::resource('sub-admins', SubAdminController::class)->except(['show']);
        });
    });
});
