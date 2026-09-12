<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tenants = '[]';
        $users = '[]';
        $userTenantPivot = '[]';
        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["ViewAny:Role","View:Role","Create:Role","Update:Role","Delete:Role","DeleteAny:Role","Restore:Role","ForceDelete:Role","ForceDeleteAny:Role","RestoreAny:Role","Replicate:Role","Reorder:Role","ViewAny:Announcement","View:Announcement","Create:Announcement","Update:Announcement","Delete:Announcement","DeleteAny:Announcement","Restore:Announcement","ForceDelete:Announcement","ForceDeleteAny:Announcement","RestoreAny:Announcement","Replicate:Announcement","Reorder:Announcement","ViewAny:Committee","View:Committee","Create:Committee","Update:Committee","Delete:Committee","DeleteAny:Committee","Restore:Committee","ForceDelete:Committee","ForceDeleteAny:Committee","RestoreAny:Committee","Replicate:Committee","Reorder:Committee","ViewAny:ContactMessage","View:ContactMessage","Create:ContactMessage","Update:ContactMessage","Delete:ContactMessage","DeleteAny:ContactMessage","Restore:ContactMessage","ForceDelete:ContactMessage","ForceDeleteAny:ContactMessage","RestoreAny:ContactMessage","Replicate:ContactMessage","Reorder:ContactMessage","ViewAny:CoreValue","View:CoreValue","Create:CoreValue","Update:CoreValue","Delete:CoreValue","DeleteAny:CoreValue","Restore:CoreValue","ForceDelete:CoreValue","ForceDeleteAny:CoreValue","RestoreAny:CoreValue","Replicate:CoreValue","Reorder:CoreValue","ViewAny:CpdRecord","View:CpdRecord","Create:CpdRecord","Update:CpdRecord","Delete:CpdRecord","DeleteAny:CpdRecord","Restore:CpdRecord","ForceDelete:CpdRecord","ForceDeleteAny:CpdRecord","RestoreAny:CpdRecord","Replicate:CpdRecord","Reorder:CpdRecord","ViewAny:Event","View:Event","Create:Event","Update:Event","Delete:Event","DeleteAny:Event","Restore:Event","ForceDelete:Event","ForceDeleteAny:Event","RestoreAny:Event","Replicate:Event","Reorder:Event","ViewAny:ExecutiveMember","View:ExecutiveMember","Create:ExecutiveMember","Update:ExecutiveMember","Delete:ExecutiveMember","DeleteAny:ExecutiveMember","Restore:ExecutiveMember","ForceDelete:ExecutiveMember","ForceDeleteAny:ExecutiveMember","RestoreAny:ExecutiveMember","Replicate:ExecutiveMember","Reorder:ExecutiveMember","ViewAny:Faq","View:Faq","Create:Faq","Update:Faq","Delete:Faq","DeleteAny:Faq","Restore:Faq","ForceDelete:Faq","ForceDeleteAny:Faq","RestoreAny:Faq","Replicate:Faq","Reorder:Faq","ViewAny:Firm","View:Firm","Create:Firm","Update:Firm","Delete:Firm","DeleteAny:Firm","Restore:Firm","ForceDelete:Firm","ForceDeleteAny:Firm","RestoreAny:Firm","Replicate:Firm","Reorder:Firm","ViewAny:GalleryImage","View:GalleryImage","Create:GalleryImage","Update:GalleryImage","Delete:GalleryImage","DeleteAny:GalleryImage","Restore:GalleryImage","ForceDelete:GalleryImage","ForceDeleteAny:GalleryImage","RestoreAny:GalleryImage","Replicate:GalleryImage","Reorder:GalleryImage","ViewAny:JobListing","View:JobListing","Create:JobListing","Update:JobListing","Delete:JobListing","DeleteAny:JobListing","Restore:JobListing","ForceDelete:JobListing","ForceDeleteAny:JobListing","RestoreAny:JobListing","Replicate:JobListing","Reorder:JobListing","ViewAny:MemberSpotlight","View:MemberSpotlight","Create:MemberSpotlight","Update:MemberSpotlight","Delete:MemberSpotlight","DeleteAny:MemberSpotlight","Restore:MemberSpotlight","ForceDelete:MemberSpotlight","ForceDeleteAny:MemberSpotlight","RestoreAny:MemberSpotlight","Replicate:MemberSpotlight","Reorder:MemberSpotlight","ViewAny:MembershipLevel","View:MembershipLevel","Create:MembershipLevel","Update:MembershipLevel","Delete:MembershipLevel","DeleteAny:MembershipLevel","Restore:MembershipLevel","ForceDelete:MembershipLevel","ForceDeleteAny:MembershipLevel","RestoreAny:MembershipLevel","Replicate:MembershipLevel","Reorder:MembershipLevel","ViewAny:NewsPost","View:NewsPost","Create:NewsPost","Update:NewsPost","Delete:NewsPost","DeleteAny:NewsPost","Restore:NewsPost","ForceDelete:NewsPost","ForceDeleteAny:NewsPost","RestoreAny:NewsPost","Replicate:NewsPost","Reorder:NewsPost","ViewAny:Partner","View:Partner","Create:Partner","Update:Partner","Delete:Partner","DeleteAny:Partner","Restore:Partner","ForceDelete:Partner","ForceDeleteAny:Partner","RestoreAny:Partner","Replicate:Partner","Reorder:Partner","ViewAny:ProgrammeEntry","View:ProgrammeEntry","Create:ProgrammeEntry","Update:ProgrammeEntry","Delete:ProgrammeEntry","DeleteAny:ProgrammeEntry","Restore:ProgrammeEntry","ForceDelete:ProgrammeEntry","ForceDeleteAny:ProgrammeEntry","RestoreAny:ProgrammeEntry","Replicate:ProgrammeEntry","Reorder:ProgrammeEntry","ViewAny:Publication","View:Publication","Create:Publication","Update:Publication","Delete:Publication","DeleteAny:Publication","Restore:Publication","ForceDelete:Publication","ForceDeleteAny:Publication","RestoreAny:Publication","Replicate:Publication","Reorder:Publication","ViewAny:ResourceItem","View:ResourceItem","Create:ResourceItem","Update:ResourceItem","Delete:ResourceItem","DeleteAny:ResourceItem","Restore:ResourceItem","ForceDelete:ResourceItem","ForceDeleteAny:ResourceItem","RestoreAny:ResourceItem","Replicate:ResourceItem","Reorder:ResourceItem","ViewAny:Slider","View:Slider","Create:Slider","Update:Slider","Delete:Slider","DeleteAny:Slider","Restore:Slider","ForceDelete:Slider","ForceDeleteAny:Slider","RestoreAny:Slider","Replicate:Slider","Reorder:Slider","ViewAny:Subscription","View:Subscription","Create:Subscription","Update:Subscription","Delete:Subscription","DeleteAny:Subscription","Restore:Subscription","ForceDelete:Subscription","ForceDeleteAny:Subscription","RestoreAny:Subscription","Replicate:Subscription","Reorder:Subscription","ViewAny:Training","View:Training","Create:Training","Update:Training","Delete:Training","DeleteAny:Training","Restore:Training","ForceDelete:Training","ForceDeleteAny:Training","RestoreAny:Training","Replicate:Training","Reorder:Training","ViewAny:User","View:User","Create:User","Update:User","Delete:User","DeleteAny:User","Restore:User","ForceDelete:User","ForceDeleteAny:User","RestoreAny:User","Replicate:User","Reorder:User","View:ManageNotificationSettings","View:ManageSiteSettings","View:SendBroadcast","View:OverviewStats","View:MembershipStatusChart","View:EventRegistrationsChart","View:RevenueChart"]}]';
        $directPermissions = '[]';

        // 1. Seed tenants first (if present)
        if (! blank($tenants) && $tenants !== '[]') {
            static::seedTenants($tenants);
        }

        // 2. Seed roles with permissions
        static::makeRolesWithPermissions($rolesWithPermissions);

        // 3. Seed direct permissions
        static::makeDirectPermissions($directPermissions);

        // 4. Seed users with their roles/permissions (if present)
        if (! blank($users) && $users !== '[]') {
            static::seedUsers($users);
        }

        // 5. Seed user-tenant pivot (if present)
        if (! blank($userTenantPivot) && $userTenantPivot !== '[]') {
            static::seedUserTenantPivot($userTenantPivot);
        }

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function seedTenants(string $tenants): void
    {
        if (blank($tenantData = json_decode($tenants, true))) {
            return;
        }

        $tenantModel = '';
        if (blank($tenantModel)) {
            return;
        }

        foreach ($tenantData as $tenant) {
            $tenantModel::firstOrCreate(
                ['id' => $tenant['id']],
                $tenant
            );
        }
    }

    protected static function seedUsers(string $users): void
    {
        if (blank($userData = json_decode($users, true))) {
            return;
        }

        $userModel = 'App\Models\User';
        $tenancyEnabled = false;

        foreach ($userData as $data) {
            // Extract role/permission data before creating user
            $roles = $data['roles'] ?? [];
            $permissions = $data['permissions'] ?? [];
            $tenantRoles = $data['tenant_roles'] ?? [];
            $tenantPermissions = $data['tenant_permissions'] ?? [];
            unset($data['roles'], $data['permissions'], $data['tenant_roles'], $data['tenant_permissions']);

            $user = $userModel::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Handle tenancy mode - sync roles/permissions per tenant
            if ($tenancyEnabled && (! empty($tenantRoles) || ! empty($tenantPermissions))) {
                foreach ($tenantRoles as $tenantId => $roleNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncRoles($roleNames);
                }

                foreach ($tenantPermissions as $tenantId => $permissionNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncPermissions($permissionNames);
                }
            } else {
                // Non-tenancy mode
                if (! empty($roles)) {
                    $user->syncRoles($roles);
                }

                if (! empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }
        }
    }

    protected static function seedUserTenantPivot(string $pivot): void
    {
        if (blank($pivotData = json_decode($pivot, true))) {
            return;
        }

        $pivotTable = '';
        if (blank($pivotTable)) {
            return;
        }

        foreach ($pivotData as $row) {
            $uniqueKeys = [];

            if (isset($row['user_id'])) {
                $uniqueKeys['user_id'] = $row['user_id'];
            }

            $tenantForeignKey = 'team_id';
            if (! blank($tenantForeignKey) && isset($row[$tenantForeignKey])) {
                $uniqueKeys[$tenantForeignKey] = $row[$tenantForeignKey];
            }

            if (! empty($uniqueKeys)) {
                DB::table($pivotTable)->updateOrInsert($uniqueKeys, $row);
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            return;
        }

        /** @var Model $roleModel */
        $roleModel = Utils::getRoleModel();
        /** @var Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        $tenancyEnabled = false;
        $teamForeignKey = 'team_id';

        foreach ($rolePlusPermissions as $rolePlusPermission) {
            $tenantId = $rolePlusPermission[$teamForeignKey] ?? null;

            // Set tenant context for role creation and permission sync
            if ($tenancyEnabled) {
                setPermissionsTeamId($tenantId);
            }

            $roleData = [
                'name' => $rolePlusPermission['name'],
                'guard_name' => $rolePlusPermission['guard_name'],
            ];

            // Include tenant ID in role data (can be null for global roles)
            if ($tenancyEnabled && ! blank($teamForeignKey)) {
                $roleData[$teamForeignKey] = $tenantId;
            }

            $role = $roleModel::firstOrCreate($roleData);

            if (! blank($rolePlusPermission['permissions'])) {
                $permissionModels = collect($rolePlusPermission['permissions'])
                    ->map(fn ($permission) => $permissionModel::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $rolePlusPermission['guard_name'],
                    ]))
                    ->all();

                $role->syncPermissions($permissionModels);
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (blank($permissions = json_decode($directPermissions, true))) {
            return;
        }

        /** @var Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        foreach ($permissions as $permission) {
            if ($permissionModel::whereName($permission['name'])->doesntExist()) {
                $permissionModel::create([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ]);
            }
        }
    }
}
