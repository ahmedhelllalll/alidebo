<?php

namespace App\Policies;

use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessProfilePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    public function view(User $user, BusinessProfile $businessProfile)
    {
        return $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, BusinessProfile $businessProfile)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, BusinessProfile $businessProfile)
    {
        return $user->isAdmin();
    }

    public function approve(User $user, BusinessProfile $businessProfile)
    {
        return $user->isAdmin();
    }

    public function reject(User $user, BusinessProfile $businessProfile)
    {
        return $user->isAdmin();
    }
}
