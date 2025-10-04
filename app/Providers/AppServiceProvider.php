<?php

namespace App\Providers;

use App\Models\PeminjamanAlat;
use App\Models\PengembalianAlat;
use App\Models\User;
use App\Observers\PeminjamanAlatObserver;
use App\Observers\PengembalianAlatObserver;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        parent::register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::define('viewApiDocs', function (User $user) {
            return true;
        });
        // Gate::policy()
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('discord', \SocialiteProviders\Google\Provider::class);
        });

        // Register PeminjamanAlat Observer
        PeminjamanAlat::observe(PeminjamanAlatObserver::class);
        PengembalianAlat::observe(PengembalianAlatObserver::class);

        // Inject Vite app script into Filament only when build assets exist
        if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))) {
            FilamentView::registerRenderHook(
                'panels::body.end',
                fn(): string => Blade::render("@vite('resources/js/app.js')")
            );
        }

        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            fn(): string => Blade::render('<link rel="stylesheet" href="{{ asset("css/custom-login.css") }}">'),
        );
    }
}
