<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ExecutiveMember;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ExecutiveMemberPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExecutiveMember');
    }

    public function view(AuthUser $authUser, ExecutiveMember $executiveMember): bool
    {
        return $authUser->can('View:ExecutiveMember');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExecutiveMember');
    }

    public function update(AuthUser $authUser, ExecutiveMember $executiveMember): bool
    {
        return $authUser->can('Update:ExecutiveMember');
    }

    public function delete(AuthUser $authUser, ExecutiveMember $executiveMember): bool
    {
        return $authUser->can('Delete:ExecutiveMember');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExecutiveMember');
    }

    public function restore(AuthUser $authUser, ExecutiveMember $executiveMember): bool
    {
        return $authUser->can('Restore:ExecutiveMember');
    }

    public function forceDelete(AuthUser $authUser, ExecutiveMember $executiveMember): bool
    {
        return $authUser->can('ForceDelete:ExecutiveMember');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExecutiveMember');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExecutiveMember');
    }

    public function replicate(AuthUser $authUser, ExecutiveMember $executiveMember): bool
    {
        return $authUser->can('Replicate:ExecutiveMember');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExecutiveMember');
    }
}
