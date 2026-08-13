<?php

use Livewire\Volt\Volt;

Volt::route('/', 'welcome')->name('home');

Volt::route('/products/{slug}', 'product-page')->name('product.view');
