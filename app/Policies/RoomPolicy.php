<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('super-administrator') ? true : null;
    }

    public function manage(User $user): bool
    {
        return $user->hasPermission('rooms.manage');
    }

    public function update(User $user, Room $room): bool
    {
        return $this->manage($user);
    }
}