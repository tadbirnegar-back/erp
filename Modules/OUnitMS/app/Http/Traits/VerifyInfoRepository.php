<?php

namespace Modules\OUnitMS\app\Http\Traits;

use Modules\AAA\app\Models\User;
use Modules\OUnitMS\app\Notifications\VerifyInfoNotification;

trait VerifyInfoRepository
{
    public function userVerified(User $user)
    {
        $notif = $user->notifications()->where('type', '=', VerifyInfoNotification::class)->first();

        if (is_null($notif)) {
            $user->notify(new VerifyInfoNotification());
            $hasConfirmed = false;
        } elseif (!$notif->read()) {
            $hasConfirmed = false;

        } else {
            $hasConfirmed = true;
        }

        return $hasConfirmed;
}
}
