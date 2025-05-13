<?php

namespace Modules\PersonMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\AAA\app\Http\Repositories\OtpRepository;
use Modules\AAA\app\Models\User;
use Modules\PersonMS\app\Http\Traits\SignaturesTrait;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\Signature;

class SignatureController extends Controller
{
    use SignaturesTrait;

    public function storeSignature(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = User::find(2174);
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

    public function sendOtpSignature(Request $request)
    {
        $user = User::find(2174);

        $otpCode = mt_rand(10000, 99999);


        $data = [
            'code' => $otpCode,
            'userID' => $user->id,
            'isUsed' => false,
            'expireDate' => Carbon::now()->addMinutes(3),

        ];

        $otp = OtpRepository::store($data);
        if (is_null($otp)) {
            return response()->json(['message' => 'خطا در ارسال رمز یکبار مصرف'], 500);

        }
        $user->notify((new OtpNotification($otpCode))->onQueue('default'));
        return response()->json(['message' => 'رمز یکبارمصرف ارسال شد']);
    }

}
