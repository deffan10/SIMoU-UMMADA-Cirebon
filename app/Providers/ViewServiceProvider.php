<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            try {
                $view->with('siteLogo', SiteSetting::logoUrl());
                $view->with('siteFavicon', SiteSetting::faviconUrl());
                $view->with('siteHasLogo', SiteSetting::hasLogo());
            } catch (\Exception $e) {
                $view->with('siteLogo', '');
                $view->with('siteFavicon', asset('favicon.ico'));
                $view->with('siteHasLogo', false);
            }
        });
    }
}
