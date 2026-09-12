<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MembershipLevel;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MembershipLevelPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MembershipLevel');
    }

    public function view(AuthUser $authUser, MembershipLevel $membershipLevel): bool
    {
        return $authUser->can('View:MembershipLevel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MembershipLevel');
    }

    public function update(AuthUser $authUser, MembershipLevel $membershipLevel): bool
    {
        return $authUser->can('Update:MembershipLevel');
    }

    public function delete(AuthUser $authUser, MembershipLevel $membershipLevel): bool
    {
        return $authUser->can('Delete:MembershipLevel');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MembershipLevel');
    }

    public function restore(AuthUser $authUser, MembershipLevel $membershipLevel): bool
    {
        return $authUser->can('Restore:MembershipLevel');
    }

    public function forceDelete(AuthUser $authUser, MembershipLevel $membershipLevel): bool
    {
        return $authUser->can('ForceDelete:MembershipLevel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MembershipLevel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MembershipLevel');
    }

    public function replicate(AuthUser $authUser, MembershipLevel $membershipLevel): bool
    {
        return $authUser->can('Replicate:MembershipLevel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MembershipLevel');
    }
}
