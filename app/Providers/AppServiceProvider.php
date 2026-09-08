<?php

namespace App\Providers;

use Filament\Panel;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;
use Lunar\Facades\Telemetry;
use Lunar\Shipping\ShippingPlugin;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        LunarPanel::register();
        LunarPanel::panel(function (Panel $panel) {
            return $panel->plugin(new ShippingPlugin());
        })->register();
    }

    public function boot(): void
    {
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        if(!config('app.lunar_telemetry')) {
            Telemetry::optOut();
        }
    }
}
