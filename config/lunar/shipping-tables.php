<?php

return [
    'enabled' => env('LUNAR_SHIPPING_TABLES_ENABLED', true),

    // 'default' uses the system-wide default tax class.
    // 'highest' selects the highest tax rate from items in the cart.
    'shipping_rate_tax_calculation' => 'default',
];
