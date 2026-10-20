<?php

namespace App\Providers;

use App\Models\ManuscriptReview;
use App\Policies\ManuscriptPolicy;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // one policy for the manuscript and its review slots
        Gate::policy(ManuscriptReview::class, ManuscriptPolicy::class);
    }
}
