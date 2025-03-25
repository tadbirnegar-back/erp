<?php

namespace Modules\AAA\app\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\Token;
use Modules\AAA\app\Http\Enums\PermissionTypesEnum;
use Modules\AAA\app\Http\Repositories\OtpRepository;
use Modules\AAA\app\Http\Traits\LoginTrait;
use Modules\AAA\app\Http\Traits\UserTrait;
use Modules\AAA\app\Models\User;
use Modules\AAA\app\Notifications\OtpNotification;
use Modules\AddressMS\app\Traits\AddressTrait;
use Modules\Gateway\app\Http\Traits\PaymentRepository;
use Modules\OUnitMS\app\Http\Traits\VerifyInfoRepository;
use Modules\OUnitMS\app\Models\VillageOfc;
use Modules\PersonMS\app\Http\Traits\PersonTrait;
use Modules\PersonMS\app\Models\Natural;
use Modules\VCM\app\Models\VcmVersions;
use Symfony\Component\HttpFoundation\Cookie;

class LoginControllerV2 extends Controller
{
    use VerifyInfoRepository, UserTrait, PersonTrait, AddressTrait, PaymentRepository, LoginTrait;

    public function getToken(Request $request)
    {
        $credentials = $request->only('mobile', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = auth()->user()->token();
            if ($token) {
                /* --------------------------- revoke access token -------------------------- */
                $token->revoke();
                $token->delete();

                /* -------------------------- revoke refresh token -------------------------- */
                $refreshTokenRepository = app(RefreshTokenRepository::class);
                $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
            }
        } else {
            return response()->json(['message' => 'نام کاربری یا رمز عبور نادرست است'], 403);
        }


        $baseUrl = url('/');

        $response = Http::post("{$baseUrl}/oauth/token", [
            'username' => $request->mobile,
            'password' => $request->password,
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
            'grant_type' => 'password'
        ]);


        $result = json_decode($response->getBody(), true);

        if (!$response->ok()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

        $cookie = new Cookie('refresh_token', $result['refresh_token'], Carbon::now()->addSeconds($result['expires_in']), null, $domain, \request()->secure(), true, true, 'none');

        unset($result['refresh_token']);
        unset($result['token_type']);
        unset($result['expires_in']);

        return response()->json($result)->withCookie($cookie);
    }

    public function getPermission()
    {
        $user = Auth::user();
        $permissions = $this->takePermissions($user);
        return response()->json($permissions);
    }

    public function getUserInfo()
    {
        $user = Auth::user();
        $userInfo = $this->takeUserInfo($user);
        return response()->json($userInfo);
    }

    public function checkPayed()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name');
        if (in_array('کاربر', $roles->toArray())) {
            $payRes = $this->userHasDebt($user);
            $result['hasPayed'] = $payRes['hasDebt'];
            $result['phaseOnePay'] = $payRes['alreadyPayed'];
            $result['confirmed'] = $this->userVerified($user);
        } else {
            $result['hasPayed'] = true;
            $result['confirmed'] = true;
        }
        return response()->json($result);
    }

    public function refreshToken(Request $request)
    {

        $validator = Validator::make($request->cookie(), [
            'refresh_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $baseUrl = url('/');

        try {
            $response = Http::post("{$baseUrl}/oauth/token", [
                'refresh_token' => $request->cookie('refresh_token'),
                'client_id' => config('passport.password_grant_client.id'),
                'client_secret' => config('passport.password_grant_client.secret'),
                'grant_type' => 'refresh_token'
            ])->throwIfStatus(fn($status) => $status >= 400);

            $result = $response->json();
            $token = $result['access_token'];
            $token_parts = explode('.', $token);
            $token_header = $token_parts[1];
            $token_header_json = base64_decode($token_header);
            $token_header_array = json_decode($token_header_json, true);
            $token_id = $token_header_array['jti'];

            $accessToken = Token::find($token_id);
            $user = User::where('mobile', '=', $accessToken->user_id)->first();

            $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

            $cookie = new Cookie('refresh_token', $result['refresh_token'], Carbon::now()->addSeconds($result['expires_in']), null, $domain, $request->secure(), true, true, 'none');

            unset($result['refresh_token']);
            unset($result['token_type']);
            unset($result['expires_in']);

            return response()->json($result)->withCookie($cookie);

        } catch (RequestException $e) {
            $errorResponse = $e->response->json();
            $errorDescription = $errorResponse['error_description'] ?? 'An error occurred.';

            if (str_contains($errorDescription, 'The refresh token is invalid')) {
                return response()->json(['error' => 'The refresh token is invalid or has expired. Please log in again.'], 401);
            }

            return response()->json(['error' => $errorDescription], 401);
        }
    }

    public function loginWithOtp(Request $request)
    {
        $user = User::where('mobile', $request->mobile)->first();

        if (is_null($user)) {
            return response()->json(['message' => 'کاربری یافت نشد'], 404);
        }

        $baseUrl = url('/');

        $response = Http::post("{$baseUrl}/oauth/token", [
            'username' => $request->mobile,
            'otp' => $request->otp,
//            'otp_verifier' => 'otp ver',
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
            'grant_type' => 'otp_grant'
        ]);

        $result = json_decode($response->getBody(), true);

        if (array_key_exists('error', $result)) {
            return response()->json(['message' => 'کد وارد شده نادرست است'], 401);
        }

        if (!$response->ok()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

        $cookie = new Cookie('refresh_token', $result['refresh_token'], Carbon::now()->addSeconds($result['expires_in']), null, $domain, \request()->secure(), true, true, 'none');

        unset($result['refresh_token']);
        unset($result['token_type']);
        unset($result['expires_in']);

        return response()->json($result)->withCookie($cookie);
    }


}
