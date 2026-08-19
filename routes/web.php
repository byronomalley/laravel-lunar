<?php

use Livewire\Volt\Volt;

Volt::route('/', 'welcome')->name('home');

Volt::route('/products/{slug}', 'product-page')->name('product.view');

Volt::route('/collections/{slug}', 'collection-page')->name('collection.view');

Volt::route('checkout', 'checkout-page')->name('checkout.view');

Volt::route('/checkout/success', 'confirmation-page')->name('checkout-success.view');
