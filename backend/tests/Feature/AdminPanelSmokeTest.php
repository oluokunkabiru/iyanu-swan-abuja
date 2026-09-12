<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class AdminPanelSmokeTest extends TestCase
{
    use UsesMysqlInTransaction;

    /**
     * @return array<string, array{0: string}>
     */
    public static function adminPages(): array
    {
        $paths = [
            'admin',
            'admin/announcements',
            'admin/committees',
            'admin/contact-messages',
            'admin/core-values',
            'admin/cpd-records',
            'admin/events',
            'admin/events/create',
            'admin/executive-members',
            'admin/faqs',
            'admin/firms',
            'admin/gallery-images',
            'admin/job-listings',
            'admin/manage-site-settings',
            'admin/member-spotlights',
            'admin/membership-levels',
            'admin/news-posts',
            'admin/news-posts/create',
            'admin/partners',
            'admin/programme-entries',
            'admin/publications',
            'admin/resource-items',
            'admin/send-broadcast',
            'admin/sliders',
            'admin/sliders/create',
            'admin/subscriptions',
            'admin/trainings',
            'admin/users',
            'admin/users/create',
        ];

        return collect($paths)->mapWithKeys(fn (string $path) => [$path => [$path]])->all();
    }

    #[DataProvider('adminPages')]
    public function test_admin_page_loads_for_authenticated_admin(string $path): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/'.$path);

        $response->assertOk();
    }

    public function test_site_settings_can_be_saved(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(ManageSiteSettings::class)
            ->fillForm(['short_name' => 'SWAN', 'parent_body' => 'ICAN'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('SWAN', SiteSetting::current()->fresh()->short_name);
    }
}
