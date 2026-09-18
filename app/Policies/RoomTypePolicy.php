<?php

namespace App\Policies;

use App\Models\RoomType;
use App\Models\User;

class RoomTypePolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('super-administrator') ? true : null;
    }

    public function manage(User $user): bool
    {
        return $user->hasPermission('rooms.manage');
    }

    public function update(User $user, RoomType $roomType): bool
    {
        return $this->manage($user);
    }
}