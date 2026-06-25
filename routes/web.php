<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'website.index')->name('website.home');
Route::view('/about', 'website.about_us')->name('website.about');
Route::view('/contact-us', 'website.contact_us')->name('website.contact');
Route::view('/privacy-policy', 'website.privacy_policy')->name('website.privacy');
Route::view('/collections/all', 'website.b2b_products')->name('website.collections.all');

Route::get('/checkout/lookup', [CheckoutController::class, 'lookup'])->middleware('throttle:30,1')->name('website.checkout.lookup');
Route::get('/checkout/{product}', [CheckoutController::class, 'show'])->name('website.checkout.show');
Route::post('/checkout/{product}/coupon', [CheckoutController::class, 'applyCoupon'])->middleware('throttle:30,1')->name('website.checkout.coupon');
Route::post('/checkout/{product}/order', [CheckoutController::class, 'createOrder'])->name('website.checkout.order');
Route::post('/checkout/payment/verify', [CheckoutController::class, 'verify'])->name('website.checkout.verify');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('website.checkout.success');

// Old routes (SEO)
Route::redirect('/b2b-products', '/collections/all', 301)->name('website.b2b');
Route::view('/collections/mini-ac', 'website.index')->name('website.legacy.collections.mini_ac');
Route::view('/products/studio-ac-0-5-ton', 'website.index')->name('website.legacy.products.studio_ac');
Route::view('/blogs/news', 'website.index')->name('website.legacy.blogs.news');
Route::view('/pages/warranty-policy', 'website.index')->name('website.legacy.pages.warranty_policy');
Route::view('/pages/contact-us', 'website.contact_us')->name('website.legacy.pages.contact_us');
Route::view('/pages/support', 'website.contact_us')->name('website.legacy.pages.support');
Route::view('/pages/about-us', 'website.about_us')->name('website.legacy.pages.about_us');
Route::view('/products/mini-ups-pro', 'website.mini_ups')->name('website.legacy.products.mini_ups_pro');
Route::view('/products/mini-ups-for-airfiber', 'website.mini_ups')->name('website.legacy.products.mini_ups_airfiber');
Route::view('/mini-ups-for-airfiber', 'website.mini_ups')->name('website.legacy.mini_ups_airfiber');
Route::view('/mini-ups', 'website.mini_ups')->name('website.mini_ups');
Route::view('/gan-charger', 'website.gan_charger')->name('website.gan_charger');
Route::view('/buy-studio-ac', 'website.buy_studio_ac')->name('website.buy_studio_ac');
Route::view('/buy-mini-ups', 'website.buy_mini_ups')->name('website.buy_mini_ups');
Route::view('/buy-mini-ups-airfiber', 'website.buy_mini_ups_airfiber')->name('website.buy_mini_ups_airfiber');
Route::view('/buy-gan-charger', 'website.buy_gan_charger')->name('website.buy_gan_charger');
