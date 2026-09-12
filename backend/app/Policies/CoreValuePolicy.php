<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CoreValue;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CoreValuePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CoreValue');
    }

    public function view(AuthUser $authUser, CoreValue $coreValue): bool
    {
        return $authUser->can('View:CoreValue');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CoreValue');
    }

    public function update(AuthUser $authUser, CoreValue $coreValue): bool
    {
        return $authUser->can('Update:CoreValue');
    }

    public function delete(AuthUser $authUser, CoreValue $coreValue): bool
    {
        return $authUser->can('Delete:CoreValue');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CoreValue');
    }

    public function restore(AuthUser $authUser, CoreValue $coreValue): bool
    {
        return $authUser->can('Restore:CoreValue');
    }

    public function forceDelete(AuthUser $authUser, CoreValue $coreValue): bool
    {
        return $authUser->can('ForceDelete:CoreValue');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CoreValue');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CoreValue');
    }

    public function replicate(AuthUser $authUser, CoreValue $coreValue): bool
    {
        return $authUser->can('Replicate:CoreValue');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CoreValue');
    }
}
