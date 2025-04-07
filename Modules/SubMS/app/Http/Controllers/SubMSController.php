<?php

namespace Modules\SubMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\AAA\app\Models\User;
use Modules\SettingsMS\app\Models\Setting;
use Modules\SubMS\app\Models\Subscription;
use Modules\SubMS\app\Http\Services\SubscriptionService;

class SubMSController extends Controller
{
    public function checkUserIsTargeted()
    {
        $user = User::find(2174);

        $subscription = new SubscriptionService($user);


        $isUserTargeted = $subscription->IsUserTargeted();

        if (empty($isUserTargeted[0])) {
            return response()->json(['isTargeted' => false]);
        }

        $subValidation = $subscription->checkSubscriptionExists($isUserTargeted[0]);

        return response()->json($subValidation);
    }

    public function paySubscription(Request $request)
    {
        $user = User::find(2174);

        $subscription = new SubscriptionService($user);

        $isUserTargeted = $subscription->IsUserTargeted();

        if (empty($isUserTargeted[0])) {
            return response()->json(['message' => 'شما دهیار هیچ دهیاری نیستید'] , 403);
        }


        $payment = $subscription->payment($request->amount, $isUserTargeted[0]);

        return response()->json($payment);

    }
}
