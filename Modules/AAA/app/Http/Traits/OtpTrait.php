<?php

namespace Modules\AAA\app\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Modules\AAA\app\Http\Enums\OtpPatternsEnum;
use Modules\AAA\app\Models\Otp;
use Modules\AAA\app\Notifications\OtpNotification;
use Modules\AAA\app\Notifications\OtpUnRegisteredNotification;

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

    public function verifyOtpByMobileMultiAccess($mobile, string|int $code)
    {
        $result = Otp::where('mobile', $mobile)
            ->where('code', $code)
            ->where('expire_date', '>', now())->first();

        return $result;
    }

    public function sendOtp(array $data, string $patternCode = OtpPatternsEnum::USER_OTP->value)
    {
        $otpData = [
            'mobile' => $data['mobile'],
            'code' => $data['code'],
            'isUsed' => false,
            'expireDate' => Carbon::now()->addMinutes($data['expire']),
        ];
        $otp = $this->storeOTP($otpData);

        $numberData = ['mobile' => $data['mobile']];

        Notification::send($numberData, (new OtpUnRegisteredNotification($otp->code , $patternCode))->onQueue('high'));
    }

    public function userOtpVerifiedByDate(string $mobile, $date)
    {
        return Otp::where('expire_date', '>=', $date)
            ->where('isUsed', true)
            ->where('mobile', $mobile)
            ->exists();
    }


}
