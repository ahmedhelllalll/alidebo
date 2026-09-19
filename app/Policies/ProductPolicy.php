<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
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

    public function view(User $user, Product $product)
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $product->businessProfile->owner_id;
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->businessProfile()->exists();
    }

    public function update(User $user, Product $product)
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $product->businessProfile->owner_id;
    }

    public function delete(User $user, Product $product)
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $product->businessProfile->owner_id;
    }

    public function approve(User $user, Product $product)
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Product $product)
    {
        return $user->isAdmin();
    }
}
