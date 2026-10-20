<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\AppSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || str_starts_with((string) config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // View Composer untuk Footer Landing Page (Cached 1 Jam)
        View::composer('landingpage.shared.footer', function ($view) {
            $footerSettings = Cache::remember('site_footer_settings', 3600, function () {
                $keys = [
                    'social_instagram',
                    'social_tiktok',
                    'social_youtube',
                    'social_facebook',
                    'social_x',
                    'contact_email',
                    'contact_phone',
                    'contact_address',
                    'contact_working_days',
                    'contact_working_hours',
                    'contact_maps_embed',
                    'contact_maps_url',
                ];

                return AppSetting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
            });

            $view->with('footerSettings', $footerSettings);
        });
    }
}
