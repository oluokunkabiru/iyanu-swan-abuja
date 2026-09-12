<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProgrammeEntry;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProgrammeEntryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProgrammeEntry');
    }

    public function view(AuthUser $authUser, ProgrammeEntry $programmeEntry): bool
    {
        return $authUser->can('View:ProgrammeEntry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProgrammeEntry');
    }

    public function update(AuthUser $authUser, ProgrammeEntry $programmeEntry): bool
    {
        return $authUser->can('Update:ProgrammeEntry');
    }

    public function delete(AuthUser $authUser, ProgrammeEntry $programmeEntry): bool
    {
        return $authUser->can('Delete:ProgrammeEntry');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProgrammeEntry');
    }

    public function restore(AuthUser $authUser, ProgrammeEntry $programmeEntry): bool
    {
        return $authUser->can('Restore:ProgrammeEntry');
    }

    public function forceDelete(AuthUser $authUser, ProgrammeEntry $programmeEntry): bool
    {
        return $authUser->can('ForceDelete:ProgrammeEntry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProgrammeEntry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProgrammeEntry');
    }

    public function replicate(AuthUser $authUser, ProgrammeEntry $programmeEntry): bool
    {
        return $authUser->can('Replicate:ProgrammeEntry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProgrammeEntry');
    }
}
