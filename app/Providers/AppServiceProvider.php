<?php

namespace App\Providers;

use App\Models\Assessment;
use App\Models\User;
use App\Policies\AssessmentPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Trust all proxies (Railway, Render, etc.) for HTTPS asset URLs
        Request::setTrustedProxies(
            ['*'],
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );

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
