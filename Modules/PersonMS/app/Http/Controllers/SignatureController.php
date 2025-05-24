<?php

namespace Modules\PersonMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Http\Enums\OtpPatternsEnum;
use Modules\AAA\app\Http\Repositories\OtpRepository;
use Modules\AAA\app\Http\Traits\OtpTrait;
use Modules\AAA\app\Models\User;
use Modules\PersonMS\app\Http\Traits\SignaturesTrait;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\Signature;

class SignatureController extends Controller
{
    use SignaturesTrait , OtpTrait;

    public function storeSignature(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = Auth::user();
            $person = Person::find($user->person_id);

            $activeStatus = $this->activeSignatureStatus();
            $notActiveStatus = $this->notActiveSignatureStatus();

            // Deactivate existing signatures
            Signature::where('person_id', $person->id)
                ->update([
                    'status_id' => $notActiveStatus->id,
                ]);

            // Prepare data using helper method
            $signatureData = $this->prepareSignatureData($data, $person->id, $activeStatus->id);

            // Create new signature
            Signature::create($signatureData);
            DB::commit();
            return response()->json(['message' => 'اطلاعات امضا با موفقیت ثبت شد']);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    private function prepareSignatureData(array $data, int $personId, int $activeStatusId): array
    {
        return [
            'person_id' => $personId,
            'signature_file_id' => $data['signatureFileID'] ?? null,
            'signature_license_id' => $data['signatureLicenseID'] ?? null,
            'status_id' => $activeStatusId,
            'created_date' => now(),
        ];
    }

    public function sendOtpSignature()
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = [
                'mobile' => $user->mobile,
                'code' => mt_rand(10000, 99999),
                'expire' => 3,
            ];
            $this->sendOtp($data , OtpPatternsEnum::SIGNATURE_OTP->value);
            DB::commit();
            return response()->json(['mobile' => $user->mobile]);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    public function sendOtpSignatureToApprove()
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $data = [
                'mobile' => $user->mobile,
                'code' => mt_rand(10000, 99999),
                'expire' => 60,
            ];
            $this->sendOtp($data , OtpPatternsEnum::SIGNATURE_OTP->value);
            DB::commit();
            return response()->json(['mobile' => $user->mobile]);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }



    public function verifyAndRevokeOtp(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $data['mobile'] = $user->mobile;
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
