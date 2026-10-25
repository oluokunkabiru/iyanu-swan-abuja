<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\ExecutiveMember;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class MemberPhotoTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_me_returns_the_photo_uploaded_to_the_member_profile_by_an_admin(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $member->memberProfile()->create([
            'membership_status' => 'active',
            'date_of_birth' => '1985-05-10',
        ]);

        Livewire::actingAs($admin)
            ->test(EditUser::class, ['record' => $member->getRouteKey()])
            ->fillForm(['memberProfile' => [
                'photo' => UploadedFile::fake()->image('member.jpg'),
                'date_of_birth' => '1985-05-10',
                'membership_status' => 'active',
            ]])
            ->call('save')
            ->assertHasNoFormErrors();

        $photo = $member->fresh()->memberProfile->getFirstMedia('photo');
        $this->assertNotNull($photo);
        Storage::disk('public')->assertExists($photo->getPathRelativeToRoot());
        $this->actingAsApi($member)->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('photoUrl', $photo->getUrl());
    }

    public function test_member_photo_upload_is_returned_immediately_and_on_subsequent_requests(): void
    {
        Storage::fake('public');
        $member = User::factory()->create();

        $response = $this->actingAsApi($member)->post('/api/me', [
            '_method' => 'PUT',
            'date_of_birth' => '1985-05-10',
            'photo' => UploadedFile::fake()->image('member.jpg'),
        ], ['Accept' => 'application/json']);

        $photo = $member->fresh()->memberProfile->getFirstMedia('photo');
        $this->assertNotNull($photo);
        Storage::disk('public')->assertExists($photo->getPathRelativeToRoot());
        $response->assertOk()->assertJsonPath('photoUrl', $photo->getUrl());
        $this->getJson('/api/me')->assertOk()->assertJsonPath('photoUrl', $photo->getUrl());
    }

    public function test_executive_photos_are_not_automatically_assigned_to_member_accounts_with_the_same_name(): void
    {
        Storage::fake('public');
        $member = User::factory()->create(['name' => 'Shared Member Name']);
        $executive = ExecutiveMember::create(['name' => 'Shared Member Name', 'position' => 'Chairperson']);
        $executive->addMedia(UploadedFile::fake()->image('executive.jpg'))->toMediaCollection('photo');

        $this->actingAsApi($member)->getJson('/api/me')
            ->assertOk()->assertJsonPath('photoUrl', null);
    }
}
