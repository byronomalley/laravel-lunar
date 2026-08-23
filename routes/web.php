<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

//Volt::route('/', 'welcome')->name('home');

Route::get('/', function () {
    return redirect('/collections/main');
});

Volt::route('/products/{slug}', 'product-page')->name('product.view');

Volt::route('/collections/{slug}', 'collection-page')->name('collection.view');

Volt::route('checkout', 'checkout-page')->name('checkout.view');

Volt::route('/checkout/success', 'confirmation-page')->name('checkout-success.view');
