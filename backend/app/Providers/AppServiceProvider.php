<?php

namespace App\Providers;

use App\Models\GalleryImage;
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

        // Livewire's default 12 MB / 5 minute limit on admin uploads is too
        // small for event gallery videos.
        config([
            'livewire.temporary_file_upload.rules' => ['required', 'file', 'max:'.GalleryImage::MAX_UPLOAD_KB],
            'livewire.temporary_file_upload.max_upload_time' => 15,
        ]);
    }
}
