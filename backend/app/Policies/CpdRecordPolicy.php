<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CpdRecord;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CpdRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CpdRecord');
    }

    public function view(AuthUser $authUser, CpdRecord $cpdRecord): bool
    {
        return $authUser->can('View:CpdRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CpdRecord');
    }

    public function update(AuthUser $authUser, CpdRecord $cpdRecord): bool
    {
        return $authUser->can('Update:CpdRecord');
    }

    public function delete(AuthUser $authUser, CpdRecord $cpdRecord): bool
    {
        return $authUser->can('Delete:CpdRecord');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CpdRecord');
    }

    public function restore(AuthUser $authUser, CpdRecord $cpdRecord): bool
    {
        return $authUser->can('Restore:CpdRecord');
    }

    public function forceDelete(AuthUser $authUser, CpdRecord $cpdRecord): bool
    {
        return $authUser->can('ForceDelete:CpdRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CpdRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CpdRecord');
    }

    public function replicate(AuthUser $authUser, CpdRecord $cpdRecord): bool
    {
        return $authUser->can('Replicate:CpdRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CpdRecord');
    }
}
