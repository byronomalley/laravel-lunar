this is a larvel/livewire app with 

- laravel v12
- lunar ecommerce v1
- volt v1
- tailwind
- Pest v4


the storage directory stores product images, and it must be permitted to show images in the browser
otherwise you will see a 403 error when looking at images

`sail artisan storage:link`

## Shipping

docs: https://docs.lunarphp.com/1.x/addons/table-rate-shipping

## Stripe

`config/lunar/stripe.php`

`config/lunar/payments.php`

`config/services.php`

**Articles**

- docs: https://docs.lunarphp.com/1.x/addons/payments/stripe
- API Keys: https://docs.stripe.com/keys

## Server

The server is hosted on IONOS: https://login.ionos.co.uk/

**Articles**

- Installing Docker on Linux Server: https://www.ionos.com/digitalguide/server/know-how/installing-and-running-docker-on-a-linux-server/
- Docker Compose: https://www.ionos.co.uk/digitalguide/server/configuration/docker-compose-tutorial/

## Search

Scout and meilisearch

config/scout.php

Synchronise index settings

php artisan scout:sync-index-settings

**Articles**

https://www.meilisearch.com/docs/getting_started/frameworks/laravel#local-development
