<?php

namespace App\Providers;

use App\Models\Assessment;
use App\Models\User;
use App\Policies\AssessmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force HTTPS scheme for all asset and route URLs
        URL::forceScheme('https');

        // Ensure SQLite database file exists (required for Railway/demo deploy)
        if (config('database.default') === 'sqlite' && ! file_exists(database_path('database.sqlite'))) {
            touch(database_path('database.sqlite'));
        }

        Gate::policy(Assessment::class, AssessmentPolicy::class);

        // Global auth gate helper for Blade
        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('evaluator', fn (User $user) => $user->isEvaluator());
        Gate::define('auditor', fn (User $user) => $user->isAuditor());
        Gate::define('user', fn (User $user) => $user->isUser());
    }
}
