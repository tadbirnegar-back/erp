<?php

namespace Modules\AAA\app\Http\Repositories;

use Carbon\Carbon;
use Modules\AAA\app\Models\Otp;

class OtpRepository
{
    public static function store(array $data)
    {
        $otp = new Otp();
        $otp->code = $data['code'];
        $otp->user_id = $data['userID'];
        $otp->isUsed = $data['isUsed'] ? 1 : 0;
        $otp->expire_date = $data['expireDate'];
        $otp->save();

        return $otp;
    }

    public static function verify(int $userID, string|int $code)
    {
        $result = Otp::where('user_id', $userID)
            ->where('code', $code)
            ->where('isUsed', false)
            ->where('expire_date', '>', now())->first();

        return $result;
    }
}
