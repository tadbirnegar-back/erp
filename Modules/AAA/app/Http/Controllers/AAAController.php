<?php

namespace Modules\AAA\app\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Modules\AAA\app\Http\Traits\OtpTrait;
use Modules\WidgetsMS\app\Http\Repositories\WidgetRepository;
use Str;
use Validator;


class AAAController extends Controller
{
    use OtpTrait;

    public function activeWidgets()
    {
        $user = \Auth::user();

//        $user->load('activeWidgets');

        $activeWidgets = $user->activeWidgets;

        $allPermissions = $activeWidgets->map(function ($widget) {
            return $widget->permission->slug; // Extract permission model
        });

        $functions = WidgetRepository::extractor($allPermissions->toArray());

        $widgetData = [];
        foreach ($functions as $key => $item) {

            $widgetData[] = [
                'name' => Str::replace('/', '', $key),
                'data' => call_user_func([$item['controller'], $item['method']
                    ]
                    , $user)];
        }

        return response()->json($widgetData);

    }

    public function widgets()
    {
        $user = \Auth::user();
//        $user->load('widgets');


        return response()->json($user->widgets);


    }


    // --------------------------------------------------------------------

    public function generateOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'string', 'min:10', 'max:10'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $otpData = [
            'mobile' => $request->mobile,
            'code' => mt_rand(10000, 99999),
            'expire' => 3,
        ];

        $this->sendOtp($otpData);

        return response()->json([
            'message' => 'رمز یکبار مصرف ارسال شد'
        ]);
    }

    public function verifyAndRevokeOtp(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'mobile' => 'required|string|min:10|max:10',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $otp = $this->verifyOtpByMobile($data['mobile'], $data['otp']);
            if (is_null($otp)) {
                return response()->json([
                    'message' => 'کد تایید وارد شده نادرست می باشد',
                ], 403);
            }
            $otp->isUsed = true;
            $otp->save();

            DB::commit();
            return response()->json([
                'message' => 'کاربر با موفقیت تأیید شد',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'کد تایید وارد شده نادرست می باشد',
            ], 500);
        }
    }

}
