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
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\Token;
use Modules\AAA\app\Models\Permission;
use Modules\AAA\app\Models\User;
use Modules\PersonMS\app\Models\Person;
use Modules\PersonMS\app\Models\Natural;
use Symfony\Component\HttpFoundation\Cookie;

class LoginController extends Controller
{

    public function userMobileExists(Request $request)
    {
        $user = User::where('mobile', '=', $request->mobile)->first();
        if (!$user) {
            return response()->json(['کاربری یافت نشد', 404]);
        }
//        return response()->json([$user->person->avatar,
//        ]);

        return response()->json([
//            'avatar' => $user->person->avatar->slug != null ? url('/') . '/' . $user->person->avatar->slug : null,
            'avatar' => 'https://tgbot.zbbo.net/uploads/2024/1/10/mWWPCCV8uc0qaxqks0iTC6NCXni8eJPW39CenjrB.jpg',
            'fullName' => 'حمید هیرو'
//            'fullName' => $user->person->display_name
        ]);

    }

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
            'expires_in' => $expiration,
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
     * @response scenario=success {
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
     * @response scenario=success {    "token_type": "Bearer",    "expires_in": 172800,    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9    "permissions": {    "مدیریت شعبه": {    "subPermission": [    {    "name": "افزودن شعبه جدید",    "slug": "/branch/add"    },    {    "name": "لیست شعب",    "slug": "/branch/list"    }    ],    "icon": "/static/media/dashboard-icon.07a6c4029fc7e3ba1641b4600e53a4d2.svg"    }    },    "userInfo": {    "firstName": "حمید",    "lastName": "هیرو",    "avatar": "/uploads/2024/1/10/XcHN0748082_laptop_512x512.png"    }    }
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
        $token = auth()->user()->token();
        if ($token) {
            /* --------------------------- revoke access token -------------------------- */
            $token->revoke();
            $token->delete();

            /* -------------------------- revoke refresh token -------------------------- */
            $refreshTokenRepository = app(RefreshTokenRepository::class);
            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
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

        $permissions=$user->permissions()->where('permission_type_id', '=', 1)->with('moduleCategory')->get();
        foreach ($permissions as $permission) {
            $sidebarItems[$permission->moduleCategory->name]['subPermission'][]=[
                'label' => $permission->name,
                'slug' => $permission->slug,
            ];
            $sidebarItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }

        $person = $user->person;
        /**
         * @var Natural $natural
         */
        $natural = $person->personable;
        $result['permissions'] = $sidebarItems;
        $result['userInfo'] = [
            'firstName' => $natural->first_name ,
            'lastName' => $natural->last_name ,
            'avatar' => $user->person->avatar->slug != null ? url('/') . '/' . $user->person->avatar->slug : null,
//            'avatar' => 'https://tgbot.zbbo.net/uploads/2024/1/10/mWWPCCV8uc0qaxqks0iTC6NCXni8eJPW39CenjrB.jpg',
        ];
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
     * @response scenario=success {
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
        // Retrieve the access token
        $token = $result['access_token'];
        $token_parts = explode('.', $token);
        $token_header = $token_parts[1];
        $token_header_json = base64_decode($token_header);
        $token_header_array = json_decode($token_header_json, true);
        $token_id = $token_header_array['jti'];

        $accessToken = Token::find($token_id);
        $user = User::where('mobile','=',$accessToken->user_id)->first();

        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

        $cookie = new Cookie('refresh_token', $result['refresh_token'], Carbon::now()->addSeconds($result['expires_in']), null, $domain, \request()->secure(), true, true, 'none');

        unset($result['refresh_token']);
        unset($result['token_type']);
        unset($result['expires_in']);

        $permissions=$user->permissions()->where('permission_type_id', '=', 1)->with('moduleCategory')->get();
        foreach ($permissions as $permission) {
            $sidebarItems[$permission->moduleCategory->name]['subPermission'][]=[
                'label' => $permission->name,
                'slug' => $permission->slug,
            ];
            $sidebarItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }

        $person = $user->person;
        /**
         * @var Natural $natural
        */
        $natural = $person->personable;
        $result['permissions'] = $sidebarItems;
        $result['userInfo'] = [
            'firstName' => $natural->first_name ,
            'lastName' => $natural->last_name  ,
            'avatar' => $user->person->avatar->slug != null ? url('/') . '/' . $user->person->avatar->slug : null,
//            'avatar' => 'https://tgbot.zbbo.net/uploads/2024/1/10/mWWPCCV8uc0qaxqks0iTC6NCXni8eJPW39CenjrB.jpg',
        ];
        return response()->json($result)->withCookie($cookie);
    }
}
