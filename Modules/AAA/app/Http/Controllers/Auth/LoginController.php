<?php

namespace Modules\AAA\app\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\RefreshTokenRepository;
use Modules\AAA\app\Models\User;
use Modules\PersonMS\app\Models\Natural;
use Symfony\Component\HttpFoundation\Cookie;

class LoginController extends Controller
{


    public function register(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'name' => 'required',
//            'email' => 'required|email|unique:users',
//            'password' => 'required'
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json($validator->errors(), 400);
//        }
//        try {
//            DB::beginTransaction();
//
//            $natural = new Natural();
//
//
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollBack();
//            // Handle the exception
//        }
        $user = new User();
        $user->mobile = $request->mobile;
//        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['data' => $user]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['mobile', 'password']);
//        $validator = Validator::make($credentials, [
//            'email' => 'required|email',
//            'password' => 'required'
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json($validator->errors(), 400);
//        }


        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $token = auth()->user()->token();

        /* --------------------------- revoke access token -------------------------- */
        $token->revoke();
        $token->delete();

        /* -------------------------- revoke refresh token -------------------------- */
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);


        /* ------------ Create a new personal access token for the user. ------------ */
        $tokenData = auth()->user()->createToken('MyApiToken');
        $token = $tokenData->accessToken;
        $expiration = $tokenData->token->expires_at->diffInSeconds(Carbon::now());

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiration
        ]);
    }

    public function getUser()
    {
        return response()->json(auth()->user());
    }

    /**
     * @return JsonResponse
     * @authenticated
     *
     *  @response scenario=success {
     *  "id": 4,
     *  "name": "Jessica Jones",
     *  "routes": ["/tasks"]
     * }
     *
     * @response 401 scenario=failure {error: Unauthorized }
     *
     * @group AAA
     *
     * @subgroup Auth
     */
    public function logout()
    {
        $token = auth()->user()->token();

        /* --------------------------- revoke access token -------------------------- */
        $token->revoke();
        $token->delete();

        /* -------------------------- revoke refresh token -------------------------- */
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);

        return response()->json(['message' => 'Logged out successfully']);
    }

    /* ----------------- get both access_token and refresh_token ---------------- */

    /**
     * @param Request $request
     * @return JsonResponse
     *
     *
     * @bodyparams mobile string required the mobile to logging with. Example: 9123456789
     * @bodyparams password string required the password of user to authorize.
     *
     *  @response scenario=success {
     *  "id": 4,
     *  "name": "Jessica Jones",
     *  "routes": ["/tasks"]
     * }
     *
     * @response 401 scenario=failure {error: Unauthorized }
     *
     * @group AAA
     *
     * @subgroup Auth
     */
    public function loginGrant(Request $request)
    {

        $credentials = $request->only('mobile', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
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
        $result['user'] = $user;
        return response()->json($result)->withCookie($cookie);


    }

    /* -------------------------- refresh access_token -------------------------- */

    /**
     * @param Request $request
     * @return JsonResponse
     * @authenticated
     *
     * @bodyparams refresh_token string required the refresh token to renew the access token
     *
     *
     *  @response scenario=success {
     *  "access_token": 'sadfafgagasegxxxxxxxxxxxxxx'
     * }
     *
     * @response 401 scenario=failure {error: refresh token not found }
     * @response 500 scenario= {error: error in generating token }
     *
     * @group AAA
     *
     * @subgroup Auth
     */
    public function refreshToken(Request $request)
    {
        $validator = Validator::make($request->cookie(), [
            'refresh_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $baseUrl = url('/');
        $response = Http::post("{$baseUrl}/oauth/token", [
            'refresh_token' => $request->cookie('refresh_token'),
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
            'grant_type' => 'refresh_token'
        ]);

        $result = json_decode($response->getBody(), true);
        if (!$response->ok()) {
            return response()->json(['error' => $result['error_description']], 500);
        }
        return response()->json($result);
    }
}
