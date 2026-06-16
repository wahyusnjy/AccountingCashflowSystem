<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(self::class, function () {
            return $this;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gunakan try-catch agar jika framework desktopnya belum siap, Laravel tidak langsung crash
        try {
            if (config('nativephp.enabled', true) && class_exists(Window::class)) {
                // Auto seed on boot if no users exist in database
                if (\Schema::hasTable('users') && \App\Models\User::count() === 0) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
                }

                Window::open()
                    ->url(url('/login'))
                    ->width(1200)
                    ->height(800);
            }
        } catch (\Throwable $e) {
            // Abaikan error saat booting awal internal CLI
        }
    }
}