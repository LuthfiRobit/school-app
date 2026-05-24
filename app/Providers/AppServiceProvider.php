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
        $this->app->bind(
            \App\Repositories\Interfaces\MasterTrackTypeRepositoryInterface::class,
            \App\Repositories\MasterTrackTypeRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\MasterAssessmentTypeRepositoryInterface::class,
            \App\Repositories\MasterAssessmentTypeRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\MasterFeeComponentRepositoryInterface::class,
            \App\Repositories\MasterFeeComponentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\SpmbConfigurationRepositoryInterface::class,
            \App\Repositories\SpmbConfigurationRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\SpmbTrackRepositoryInterface::class,
            \App\Repositories\SpmbTrackRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\SpmbTrackFeeRepositoryInterface::class,
            \App\Repositories\SpmbTrackFeeRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\SpmbTrackAssessmentRepositoryInterface::class,
            \App\Repositories\SpmbTrackAssessmentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\SpmbTrackFormFieldRepositoryInterface::class,
            \App\Repositories\SpmbTrackFormFieldRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\ApplicantRepositoryInterface::class,
            \App\Repositories\ApplicantRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\ApplicantEnrollmentRepositoryInterface::class,
            \App\Repositories\ApplicantEnrollmentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\InvoiceRepositoryInterface::class,
            \App\Repositories\InvoiceRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\PaymentRepositoryInterface::class,
            \App\Repositories\PaymentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\AssessmentRepositoryInterface::class,
            \App\Repositories\AssessmentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\StatusLogRepositoryInterface::class,
            \App\Repositories\StatusLogRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\ReportRepositoryInterface::class,
            \App\Repositories\ReportRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\DashboardRepositoryInterface::class,
            \App\Repositories\DashboardRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\BankAccountRepositoryInterface::class,
            \App\Repositories\BankAccountRepository::class
        );
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
