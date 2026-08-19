# mamicha77s

Ebay site: https://www.ebay.co.uk/usr/mamicha77s

this is a larvel/livewire app with 

- laravel v12
- lunar ecommerce v1
- volt v1
- tailwind
- Pest v4


the storage directory stores product images, and it must be permitted to show images in the browser
otherwise you will see a 403 error when looking at images

`sail artisan storage:link`

For the checkout page the livewire view component is at: resources/views/pages/checkout-page.blade.php
sub components are at: resources/views/components/checkout

these are the composer specs:

    "require": {
        "php": "^8.2",
        "laravel/framework": "^12.0",
        "laravel/tinker": "^2.10.1",
        "livewire/livewire": "^3.8",
        "livewire/volt": "^1.11",
        "lunarphp/lunar": "^1.0"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/pail": "^1.2.2",
        "laravel/pint": "^1.24",
        "laravel/sail": "^1.66",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.6",
        "pestphp/pest": "^4.7"
    },

shipping address state is not coming through the various fields at checkout
