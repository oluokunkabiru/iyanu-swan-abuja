<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\JobListing;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class JobListingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JobListing');
    }

    public function view(AuthUser $authUser, JobListing $jobListing): bool
    {
        return $authUser->can('View:JobListing');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JobListing');
    }

    public function update(AuthUser $authUser, JobListing $jobListing): bool
    {
        return $authUser->can('Update:JobListing');
    }

    public function delete(AuthUser $authUser, JobListing $jobListing): bool
    {
        return $authUser->can('Delete:JobListing');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:JobListing');
    }

    public function restore(AuthUser $authUser, JobListing $jobListing): bool
    {
        return $authUser->can('Restore:JobListing');
    }

    public function forceDelete(AuthUser $authUser, JobListing $jobListing): bool
    {
        return $authUser->can('ForceDelete:JobListing');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JobListing');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JobListing');
    }

    public function replicate(AuthUser $authUser, JobListing $jobListing): bool
    {
        return $authUser->can('Replicate:JobListing');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JobListing');
    }
}
