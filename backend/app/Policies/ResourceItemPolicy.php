<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ResourceItem;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ResourceItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ResourceItem');
    }

    public function view(AuthUser $authUser, ResourceItem $resourceItem): bool
    {
        return $authUser->can('View:ResourceItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ResourceItem');
    }

    public function update(AuthUser $authUser, ResourceItem $resourceItem): bool
    {
        return $authUser->can('Update:ResourceItem');
    }

    public function delete(AuthUser $authUser, ResourceItem $resourceItem): bool
    {
        return $authUser->can('Delete:ResourceItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ResourceItem');
    }

    public function restore(AuthUser $authUser, ResourceItem $resourceItem): bool
    {
        return $authUser->can('Restore:ResourceItem');
    }

    public function forceDelete(AuthUser $authUser, ResourceItem $resourceItem): bool
    {
        return $authUser->can('ForceDelete:ResourceItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ResourceItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ResourceItem');
    }

    public function replicate(AuthUser $authUser, ResourceItem $resourceItem): bool
    {
        return $authUser->can('Replicate:ResourceItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ResourceItem');
    }
}
