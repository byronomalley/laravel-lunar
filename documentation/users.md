## Admin user registration

`php artisan app:create-admin-user`

## Coming Soon page

Only staff members can see the website as normal
Everyone else sees the coming soon page

Change .env variable: `APP_COMING_SOON=true`

`App\Http\Middleware\ComingSoonMiddleware`

```php
Auth::guard('staff');
```
