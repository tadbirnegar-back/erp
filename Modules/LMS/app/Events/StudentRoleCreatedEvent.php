<?php

namespace Modules\LMS\app\Events;

use Modules\AAA\app\Models\Role;
use Modules\AAA\app\Models\UserRole;

class StudentRoleCreatedEvent
{
    public int $userID;

    public function __construct($userID)
    {
        $this->userID = $userID;
        $studentRole = Role::where('name', 'فراگیر')->first();

        if ($studentRole) {
            $existingRole = UserRole::
            where('user_id', $userID)
                ->where('role_id', $studentRole->id)
                ->exists();

            if (!$existingRole) {
                UserRole::insert([
                    'user_id' => $userID,
                    'role_id' => $studentRole->id,
                ]);
            }
        }

    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
