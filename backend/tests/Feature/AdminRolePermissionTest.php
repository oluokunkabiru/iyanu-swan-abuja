<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class AdminRolePermissionTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_an_admin_with_no_panel_role_cannot_view_any_resource(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/news-posts')->assertForbidden();
    }

    public function test_an_admin_with_a_scoped_role_can_only_access_permitted_resources(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $role = Role::create(['name' => 'News Editor', 'guard_name' => 'web']);
        $role->givePermissionTo(['ViewAny:NewsPost', 'Create:NewsPost']);

        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole($role);

        $this->actingAs($admin)->get('/admin/news-posts')->assertOk();
        $this->actingAs($admin)->get('/admin/events')->assertForbidden();
    }

    public function test_super_admin_can_access_every_resource_regardless_of_explicit_grants(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/news-posts')->assertOk();
        $this->actingAs($admin)->get('/admin/events')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }

    public function test_a_super_admin_can_assign_a_panel_role_to_another_admin_from_the_users_form(): void
    {
        $superAdmin = User::factory()->admin()->create();
        $role = Role::create(['name' => 'News Editor', 'guard_name' => 'web']);
        $target = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($superAdmin)
            ->test(EditUser::class, ['record' => $target->getRouteKey()])
            ->fillForm(['roles' => [$role->id]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($target->fresh()->hasRole('News Editor'));
    }
}
