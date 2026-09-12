<?php

namespace App\Providers;

use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

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
        // Role lives in Spatie's own namespace, so Laravel's convention-based
        // policy discovery (App\Models\X -> App\Policies\XPolicy) never finds
        // it — Shield generated RolePolicy but flagged it as needing this.
        Gate::policy(Role::class, RolePolicy::class);
    }
}
