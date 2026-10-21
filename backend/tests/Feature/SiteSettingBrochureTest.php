<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class SiteSettingBrochureTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_settings_return_null_when_no_brochure_is_uploaded(): void
    {
        Storage::fake('public');
        SiteSetting::current()->clearMediaCollection('brochure');

        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('brochureUrl', null);
    }

    public function test_admin_can_upload_a_brochure_and_publish_it_through_settings(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $brochure = UploadedFile::fake()->createWithContent('brochure.pdf', "%PDF-1.4\nChapter brochure\n%%EOF");

        Livewire::actingAs($admin)
            ->test(ManageSiteSettings::class)
            ->fillForm([
                'short_name' => 'SWAN',
                'parent_body' => 'ICAN',
                'brochure' => $brochure,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $media = SiteSetting::current()->getFirstMedia('brochure');
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('brochureUrl', $media->getUrl());
    }

    public function test_uploading_a_new_brochure_replaces_the_old_brochure(): void
    {
        Storage::fake('public');
        $settings = SiteSetting::current();
        $old = $settings->addMediaFromString("%PDF-1.4\nOld brochure\n%%EOF")
            ->usingFileName('old-brochure.pdf')
            ->toMediaCollection('brochure');

        $replacement = $settings->addMediaFromString("%PDF-1.4\nNew brochure\n%%EOF")
            ->usingFileName('new-brochure.pdf')
            ->toMediaCollection('brochure');

        $this->assertModelMissing($old);
        $this->assertModelExists($replacement);
        Storage::disk('public')->assertMissing($old->getPathRelativeToRoot());
        Storage::disk('public')->assertExists($replacement->getPathRelativeToRoot());
        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('brochureUrl', $replacement->getUrl());
    }

    public function test_admin_cannot_upload_a_non_pdf_brochure(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $originalMediaIds = SiteSetting::current()->getMedia('brochure')->modelKeys();

        Livewire::actingAs($admin)
            ->test(ManageSiteSettings::class)
            ->fillForm([
                'short_name' => 'SWAN',
                'parent_body' => 'ICAN',
                'brochure' => UploadedFile::fake()->createWithContent('brochure.txt', 'This is not a PDF.'),
            ])
            ->call('save')
            ->assertHasFormErrors([
                'brochure' => fn (array $rules, array $messages): bool => str_contains(implode(' ', $messages), 'application/pdf'),
            ]);

        $this->assertSame($originalMediaIds, SiteSetting::current()->getMedia('brochure')->modelKeys());
    }

    public function test_admin_cannot_upload_a_brochure_larger_than_ten_megabytes(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $originalMediaIds = SiteSetting::current()->getMedia('brochure')->modelKeys();
        $brochure = UploadedFile::fake()->createWithContent('brochure.pdf', "%PDF-1.4\n".str_repeat(' ', 10240 * 1024));

        Livewire::actingAs($admin)
            ->test(ManageSiteSettings::class)
            ->fillForm([
                'short_name' => 'SWAN',
                'parent_body' => 'ICAN',
                'brochure' => $brochure,
            ])
            ->call('save')
            ->assertHasFormErrors(['brochure']);

        $this->assertSame($originalMediaIds, SiteSetting::current()->getMedia('brochure')->modelKeys());
    }
}
