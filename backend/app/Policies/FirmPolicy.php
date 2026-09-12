<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Firm;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class FirmPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Firm');
    }

    public function view(AuthUser $authUser, Firm $firm): bool
    {
        return $authUser->can('View:Firm');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Firm');
    }

    public function update(AuthUser $authUser, Firm $firm): bool
    {
        return $authUser->can('Update:Firm');
    }

    public function delete(AuthUser $authUser, Firm $firm): bool
    {
        return $authUser->can('Delete:Firm');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Firm');
    }

    public function restore(AuthUser $authUser, Firm $firm): bool
    {
        return $authUser->can('Restore:Firm');
    }

    public function forceDelete(AuthUser $authUser, Firm $firm): bool
    {
        return $authUser->can('ForceDelete:Firm');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Firm');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Firm');
    }

    public function replicate(AuthUser $authUser, Firm $firm): bool
    {
        return $authUser->can('Replicate:Firm');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Firm');
    }
}
