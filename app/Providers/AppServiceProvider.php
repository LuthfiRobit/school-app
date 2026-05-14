<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\AcademicYearRepositoryInterface::class,
            \App\Repositories\AcademicYearRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\SchoolIdentityRepositoryInterface::class,
            \App\Repositories\SchoolIdentityRepository::class
        );

        $this->app->bind(\App\Repositories\Interfaces\RoleRepositoryInterface::class, \App\Repositories\RoleRepository::class);
        $this->app->bind(\App\Repositories\Interfaces\UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Developer" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Developer') ? true : null;
        });
    }
}
