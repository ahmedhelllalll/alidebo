<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        // Allow admins to bypass view/create policies, but strictly enforce update/delete rules
        if ($user->isAdmin() && !in_array($ability, ['update', 'delete'])) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return false;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $admin, User $targetUser)
    {
        if (!$admin->isAdmin()) {
            return false;
        }

        return true;
    }

    public function delete(User $admin, User $targetUser)
    {
        if (!$admin->isAdmin()) {
            return false;
        }

        // Prevent deleting oneself
        if ($admin->id === $targetUser->id) {
             return false;
        }
        
        return true;
    }
}
