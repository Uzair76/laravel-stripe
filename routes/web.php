<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function (Request $request) {
    $email = 'test@example.com';
    $password = 'password';

    if (Auth::attempt(['email' => $email, 'password' => $password])) {
        return 'logged in';
    }

    return 'not logged in';
})->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', function () {
        $stripePriceId = config('services.stripe.price_id');
        if (blank($stripePriceId)) {
            return response('Set STRIPE_PRICE_ID in .env with a valid Stripe price ID (price_...).', 422);
        }

        try {
            return Auth::user()->checkout([$stripePriceId => 1], [
                'success_url' => route('checkout-success'),
                'cancel_url' => route('checkout-cancel'),
            ]);
        } catch (\Stripe\Exception\InvalidRequestException $exception) {
            return response($exception->getMessage().' Ensure STRIPE_PRICE_ID uses a valid price_... ID.', 422);
        }
    })->name('checkout');

    Route::get('/checkout/success', function () {
        return 'success';
    })->name('checkout-success');

    Route::get('/checkout/cancel', function () {
        return 'canceled';
    })->name('checkout-cancel');
});
