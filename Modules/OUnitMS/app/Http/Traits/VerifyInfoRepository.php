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
        $notif = $user->notifications()->where('type', '=', VerifyInfoNotification::class)->first();

        if (is_null($notif)) {
            $user->notify((new VerifyInfoNotification())->onQueue('default'));
            $username = Person::find($user->person_id)->display_name;
            $user->notify((new VerifyPanelNotification($username))->onQueue('default'));
            $hasConfirmed = false;
        } elseif (!$notif->read()) {
            $hasConfirmed = false;

        } else {
            $hasConfirmed = true;
        }

        return $hasConfirmed;
    }
}
