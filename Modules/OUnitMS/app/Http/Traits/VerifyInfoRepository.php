<?php

namespace Modules\OUnitMS\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\OUnitMS\app\Notifications\VerifyInfoNotification;
use Modules\OUnitMS\App\Notifications\VerifyPanelNotification;
use Modules\PersonMS\app\Models\Person;

trait VerifyInfoRepository
{
    public function userVerified(User $user)
    {
        $notif = $user->notifications()->where('type', '=', VerifyInfoNotification::class)->first();

        if (is_null($notif)) {
            $user->notify(new VerifyInfoNotification());
            $username = Person::find($user->person_id)->display_name;
            $user->notify(new VerifyPanelNotification($username));
            $hasConfirmed = false;
        } elseif (!$notif->read()) {
            $hasConfirmed = false;

        } else {
            $hasConfirmed = true;
        }

        return $hasConfirmed;
    }
}
