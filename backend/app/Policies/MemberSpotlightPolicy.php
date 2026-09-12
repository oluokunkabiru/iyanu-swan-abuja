<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MemberSpotlight;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MemberSpotlightPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MemberSpotlight');
    }

    public function view(AuthUser $authUser, MemberSpotlight $memberSpotlight): bool
    {
        return $authUser->can('View:MemberSpotlight');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MemberSpotlight');
    }

    public function update(AuthUser $authUser, MemberSpotlight $memberSpotlight): bool
    {
        return $authUser->can('Update:MemberSpotlight');
    }

    public function delete(AuthUser $authUser, MemberSpotlight $memberSpotlight): bool
    {
        return $authUser->can('Delete:MemberSpotlight');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MemberSpotlight');
    }

    public function restore(AuthUser $authUser, MemberSpotlight $memberSpotlight): bool
    {
        return $authUser->can('Restore:MemberSpotlight');
    }

    public function forceDelete(AuthUser $authUser, MemberSpotlight $memberSpotlight): bool
    {
        return $authUser->can('ForceDelete:MemberSpotlight');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MemberSpotlight');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MemberSpotlight');
    }

    public function replicate(AuthUser $authUser, MemberSpotlight $memberSpotlight): bool
    {
        return $authUser->can('Replicate:MemberSpotlight');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MemberSpotlight');
    }
}
