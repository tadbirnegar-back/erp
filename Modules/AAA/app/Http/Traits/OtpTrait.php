<?php

namespace Modules\AAA\app\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Modules\AAA\app\Models\Otp;
use Modules\AAA\app\Notifications\OtpNotification;

trait OtpTrait
{
    public function storeOTP(array $data)
    {
        $otp = new Otp();
        $otp->code = $data['code'];
        $otp->isUsed = false;
        $otp->mobile = $data['mobile'];
        $otp->expire_date = $data['expireDate'];
        $otp->save();

        return $otp;
    }

    public function verifyOtpByMobile($mobile, string|int $code)
    {
        $result = Otp::where('mobile', $mobile)
            ->where('code', $code)
            ->where('isUsed', false)
            ->where('expire_date', '>', now())->first();

        return $result;
    }

    public function sendOtp(array $data)
    {
        $otpData = [
            'mobile' => $data['mobile'],
            'code' => $data['code'],
            'isUsed' => false,
            'expireDate' => Carbon::now()->addMinutes($data['expire']),
        ];
        $otp = $this->storeOTP($otpData);

        Notification::send($otpData['mobile'], (new OtpNotification($otp->code))->onQueue('high'));
    }

    public function userOtpVerifiedByDate(string $mobile, $date)
    {
        return Otp::where('expire_date', '>=', $date)
            ->where('isUsed', true)
            ->where('mobile', $mobile)
            ->exists();
    }


}
