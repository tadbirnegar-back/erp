<?php

namespace Modules\OUnitMS\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Notifications\VerifyInfoNotification;
use Modules\PersonMS\app\Notifications\VerifyPanelNotification;

trait VerifyInfoRepository
{
    public function userVerified(User $user)
    {
        $notif = $user->notifications()->where('type', VerifyInfoNotification::class)
            ->orderBy('created_at', 'asc')
            ->first();

        if (is_null($notif)) {
            // If no notification exists, send new ones
            $user->notify(new VerifyInfoNotification());
            $username = Person::find($user->person_id)->display_name;
            $user->notify((new VerifyPanelNotification($username)));
            $hasConfirmed = false; // User is not confirmed yet
        } elseif (!$notif->read_at) { // Check if the notification has been read
            $hasConfirmed = false; // Notification exists but not read
        } else {
            $hasConfirmed = true; // Notification exists and is read
        }

        return $hasConfirmed;
    }
}
