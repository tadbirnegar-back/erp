<?php

namespace Modules\SubMS\app\Http\Services;

use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Http\Enums\RecruitmentScriptStatusEnum;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\SettingsMS\app\Models\Setting;
use Modules\SubMS\app\Models\Subscription;

class SubscriptionService
{
    use RecruitmentScriptTrait;

    public User $user;
    private Setting $setting;

    public function __construct($user)
    {
        $this->user = $user;
        $this->setting = Setting::where('key', 'subscription_fee')->first();
    }


    public function IsUserTargeted()
    {
        $villages = $this->user->load(['activeDehyarRcs' => function ($query) {
            $query->whereHas('latestStatus', function ($query) {
                $query->where('name', RecruitmentScriptStatusEnum::ACTIVE->value);
            });
        }]);

        $organIds = $villages?->activeDehyarRcs->pluck('organization_unit_id')->toArray();

        return [$organIds, $this->setting->value];
    }


    public function checkSubscriptionExists(array $villageOfcs = [])
    {
        $villageCounts = count($villageOfcs);
        $subscription = Subscription::whereIn('ounit_id', $villageOfcs)->where('expire_date', '>', now())->get();
        if (empty($subscription[0])) {
            return ['isTargeted' => true, 'price' => $this->setting->value * $villageCounts];
        }
        $order = new OrderService($subscription, $this->setting->value, $villageCounts);
        return $order->checkOrderForSubscription();
    }


    public function paySubscription()
    {

    }
}
