<?php

namespace Modules\AAA\app\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\Token;
use Modules\AAA\app\Http\Repositories\OtpRepository;
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

class LoginController extends Controller
{
    use VerifyInfoRepository, UserTrait, PersonTrait, AddressTrait, PaymentRepository;

    /**
     * Check if a user's mobile number exists.
     * @Authenticated
     * @group AAA
     *
     * @subgroup User Existance
     * @bodyParam mobile string required The mobile number of the user.
     *
     * @response 200 {
     *     "avatar": "http://example.com/avatar.jpg",
     *     "fullName": "John Doe"
     * }
     *
     * @response 422 {
     *     "errors": {
     *         "mobile": [
     *             "The mobile field is required."
     *         ]
     *     }
     * }
     */
    public function userMobileExists(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'mobile' => [
                'required',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->mobileExists($request->mobile);
        if ($user === null) {
            return response()->json(['message' => 'کاربری یافت نشد'], 404);
        } elseif ($user->latestStatus->name === 'غیرفعال') {
            return response()->json(['message' => 'کاربر غیرفعال است', 'data' => $user->person->display_name], 423);
        }

        return response()->json([
            'avatar' => (!is_null($user?->person->avatar)) ? $user->person->avatar->slug : null,
            'fullName' => $user->person->display_name
        ]);

    }

    public function isPersonUser(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'nationalCode' => [
                'required',
            ],

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $result = $this->naturalPersonExists($request->nationalCode);

        if ($result === null) {
            $message = 'notFound';
            $data = null;
        } else {
            $user = $result->user;

            if ($user) {
                $user->load('person');
                $message = 'user';
                $data = $user;
            } else {
                $message = 'found';
                $data = $result;
            }
        }

        return response()->json(['data' => $data, 'message' => $message]);
    }

    public function register(Request $request)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'mobile' => [
                'required',
                'unique:users,mobile',
            ],
            'email' => [
                'sometimes',
                'unique:users,email',
            ],
            'username' => [
                'sometimes',
                'unique:users,username',
            ],
            'nationalCode' => [
                'required',
                'unique:persons,national_code',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data['userID'] = Auth::user()->id;

        $data['roles'] = isset($data['roles']) ? json_decode($data['roles']) : null;


        if ($request->isNewPerson) {

            if ($request->isNewAddress) {

                $address = $this->addressStore($data);

                $data['homeAddressID'] = $address->id;
            }
            $natural = $this->naturalStore($data);


            $data['personID'] = $natural->person->id;

        }
        $user = $this->storeUser($data);


        return response()->json(['data' => $user]);
    }


    public function getUser()
    {
        return response()->json(auth()->user());
    }

    /**
     * @return JsonResponse
     * @authenticated
     *
     * @response scenario=success { "id": 4,"name": "Jessica Jones", "routes": ["/tasks"] }
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


        $sidebarPermissions = $user->permissions()->where('permission_type_id', '=', 1)->orderBy('priority', 'asc') // Sort by 'priority' in ascending order
        ->with('moduleCategory')->get();
        foreach ($sidebarPermissions as $permission) {
            $sidebarItems[$permission->moduleCategory->name]['subPermission'][] = [
                'label' => $permission?->name,
                'slug' => $permission?->slug,
            ];
            $sidebarItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }


        $operationalPermissions = $user->permissions()->where('permission_type_id', '=', 2)->with('moduleCategory')->get();
        foreach ($operationalPermissions as $permission) {
            $operationalItems[$permission->moduleCategory->name]['subPermission'][] = [
                'label' => $permission?->name,
                'slug' => $permission?->slug,
            ];
            $operationalItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }

//        $permissions = $user->permissions()->with(['moduleCategory', 'permissionTypes'])->get();


        $person = $user->person;
        /**
         * @var Natural $natural
         */
        $natural = $person->personable;
//        $result['permissions'] = $permissions->groupBy('permissionTypes.name');
        $result['operational'] = $operationalItems ?? null;
        $result['sidebar'] = $sidebarItems ?? null;

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


        $version = VcmVersions::orderBy('id', 'desc')->first();

        if(is_null($version)){
            $versionTxt = '1.0.0';
        }else{
            $versionTxt = $version->high_version.'.'.$version->mid_version.'.'.$version->low_version;
        }
        $result['version'] = ["version" => $versionTxt];
        $result['userInfo'] = [
            'firstName' => $natural->first_name,
            'lastName' => $natural->last_name,
            'avatar' => !is_null($user->person->avatar) ? $user->person->avatar->slug : null,
//            'avatar' => 'https://tgbot.zbbo.net/uploads/2024/1/10/mWWPCCV8uc0qaxqks0iTC6NCXni8eJPW39CenjrB.jpg',
            $result['roles'] = $user->roles,

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

        if (is_null($response)) {
            return response()->json(['error' => 'error'], 401);
        }

        if ($response->failed()) {
            $result = $response->json();
            $errorDescription = $result['error_description'] ?? 'An error occurred.';
            $statusCode = $response->status();

            // Check for specific error messages
            if (str_contains($errorDescription, 'The refresh token is invalid')) {
                return response()->json(['error' => 'The refresh token is invalid or has expired. Please log in again.'], 401);
            }

            return response()->json(['error' => $errorDescription], $statusCode);
        }
        $result = json_decode($response?->getBody(), true);
        if (!$response->ok()) {
            return response()->json(['error' => $result['error_description'] ?? null], 401);
        }
        // Retrieve the access token
        $token = $result['access_token'];
        $token_parts = explode('.', $token);
        $token_header = $token_parts[1];
        $token_header_json = base64_decode($token_header);
        $token_header_array = json_decode($token_header_json, true);
        $token_id = $token_header_array['jti'];

        $accessToken = Token::find($token_id);
        $user = User::where('mobile', '=', $accessToken->user_id)->first();

        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;

        $cookie = new Cookie('refresh_token', $result['refresh_token'], Carbon::now()->addSeconds($result['expires_in']), null, $domain, \request()->secure(), true, true, 'none');

        unset($result['refresh_token']);
        unset($result['token_type']);
        unset($result['expires_in']);

        $sidebarPermissions = $user->permissions()->where('permission_type_id', '=', 1)->with('moduleCategory')->get();
        foreach ($sidebarPermissions as $permission) {
            $sidebarItems[$permission->moduleCategory->name]['subPermission'][] = [
                'label' => $permission?->name,
                'slug' => $permission?->slug,
            ];
            $sidebarItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }

        $operationalPermissions = $user->permissions()->where('permission_type_id', '=', 2)->with('moduleCategory')->get();
        foreach ($operationalPermissions as $permission) {
            $operationalItems[$permission->moduleCategory->name]['subPermission'][] = [
                'label' => $permission?->name,
                'slug' => $permission?->slug,
            ];
            $operationalItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }

//        $permissions = $user->permissions()->with(['moduleCategory', 'permissionTypes'])->get();


        $person = $user->person;
        /**
         * @var Natural $natural
         */
        $natural = $person->personable;
//        $result['permissions'] = $permissions->groupBy('permissionTypes.name');
        $result['operational'] = $operationalItems ?? null;
        $result['sidebar'] = $sidebarItems ?? null;
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

        $version = VcmVersions::orderBy('id', 'desc')->first();

        if(is_null($version)){
            $versionTxt = '1.0.0';
        }else{
            $versionTxt = $version->high_version.'.'.$version->mid_version.'.'.$version->low_version;
        }
        $result['version'] = ["version" => $versionTxt];
        $result['userInfo'] = [
            'firstName' => $natural->first_name,
            'lastName' => $natural->last_name,
            'avatar' => $user->person->avatar != null ? $user->person->avatar->slug : null,
            $result['roles'] = $user->roles,

        ];
        return response()->json($result)->withCookie($cookie);

    }

    /* -------------------------- login by otp -------------------------- */

    public function generateOtp(Request $request)
    {
        $user = User::where('mobile', $request->mobile)->first();

        if (is_null($user)) {
            return response()->json(['message' => 'کاربری یافت نشد'], 404);
        }

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

    public function otpLogin(Request $request)
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

        $sidebarPermissions = $user->permissions()->where('permission_type_id', '=', 1)->with('moduleCategory')->get();
        foreach ($sidebarPermissions as $permission) {
            $sidebarItems[$permission->moduleCategory->name]['subPermission'][] = [
                'label' => $permission?->name,
                'slug' => $permission?->slug,
            ];
            $sidebarItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }

        $operationalPermissions = $user->permissions()->where('permission_type_id', '=', 2)->with('moduleCategory')->get();
        foreach ($operationalPermissions as $permission) {
            $operationalItems[$permission->moduleCategory->name]['subPermission'][] = [
                'label' => $permission?->name,
                'slug' => $permission?->slug,
            ];
            $operationalItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }

//        $permissions = $user->permissions()->with(['moduleCategory', 'permissionTypes'])->get();


        $person = $user->person;
        /**
         * @var Natural $natural
         */
        $natural = $person->personable;
//        $result['permissions'] = $permissions->groupBy('permissionTypes.name');
        $result['operational'] = $operationalItems ?? null;
        $result['sidebar'] = $sidebarItems ?? null;
        $result['userInfo'] = [
            'firstName' => $natural->first_name,
            'lastName' => $natural->last_name,
            'avatar' => !is_null($user->person->avatar) ? $user->person->avatar->slug : null,
//            'avatar' => 'https://tgbot.zbbo.net/uploads/2024/1/10/mWWPCCV8uc0qaxqks0iTC6NCXni8eJPW39CenjrB.jpg',
            $result['roles'] = $user->roles,

        ];
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
        return response()->json($result)->withCookie($cookie);

    }

    public function handleLogin(Request $request)
    {
        $user = null;
        $grantType = null;

        // Determine grant type based on the request
        if ($request->has('password')) {
            $grantType = 'password';
        } elseif ($request->has('otp')) {
            $grantType = 'otp';
        } elseif ($request->has('refresh_token')) {
            $grantType = 'refresh_token';
        } else {
            return response()->json(['message' => 'Invalid grant type'], 400);
        }

        // Handle different grant types
        if ($grantType === 'password' && Auth::attempt($request->only('mobile', 'password'))) {
            $user = Auth::user();
        } elseif ($grantType === 'otp') {
            $user = User::where('mobile', $request->mobile)->first();
            if (is_null($user)) {
                return response()->json(['message' => 'کاربری یافت نشد'], 404);
            }
        } elseif ($grantType === 'refresh_token') {
            $user = auth()->user(); // Assuming the refresh token is valid and belongs to the user
        }

        if (!$user) {
            return response()->json(['message' => 'نام کاربری یا رمز عبور نادرست است'], 401);
        }

        // Revoke existing token if any
        $token = auth()->user()->token();
        if ($token) {
            $token->revoke();
            $token->delete();
            $refreshTokenRepository = app(RefreshTokenRepository::class);
            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
        }

        // Request a new token
        $baseUrl = url('/');
        $tokenRequestData = [
            'client_id' => config('passport.password_grant_client.id'),
            'client_secret' => config('passport.password_grant_client.secret'),
            'grant_type' => $grantType
        ];

        if ($grantType === 'password') {
            $tokenRequestData['username'] = $request->mobile;
            $tokenRequestData['password'] = $request->password;
        } elseif ($grantType === 'otp') {
            $tokenRequestData['username'] = $request->mobile;
            $tokenRequestData['otp'] = $request->otp;
        } elseif ($grantType === 'refresh_token') {
            $tokenRequestData['refresh_token'] = $request->refresh_token;
        }

        $response = Http::post("{$baseUrl}/oauth/token", $tokenRequestData);
        $result = json_decode($response->getBody(), true);

        if (!$response->ok() || array_key_exists('error', $result)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Set refresh token cookie
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        $cookie = new Cookie('refresh_token', $result['refresh_token'], Carbon::now()->addSeconds($result['expires_in']), null, $domain, \request()->secure(), true, true, 'none');

        unset($result['refresh_token'], $result['token_type'], $result['expires_in']);

        // Set permissions and user info
        $sidebarPermissions = $user->permissions()->where('permission_type_id', '=', 1)->with('moduleCategory')->get();
        foreach ($sidebarPermissions as $permission) {
            $sidebarItems[$permission->moduleCategory->name]['subPermission'][] = [
                'label' => $permission?->name,
                'slug' => $permission?->slug,
            ];
            $sidebarItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }

        $operationalPermissions = $user->permissions()->where('permission_type_id', '=', 2)->with('moduleCategory')->get();
        foreach ($operationalPermissions as $permission) {
            $operationalItems[$permission->moduleCategory->name]['subPermission'][] = [
                'label' => $permission?->name,
                'slug' => $permission?->slug,
            ];
            $operationalItems[$permission->moduleCategory->name]['icon'] = $permission->moduleCategory->icon;
        }

        $person = $user->person;
        $natural = $person->personable;

        $result['operational'] = $operationalItems ?? null;
        $result['sidebar'] = $sidebarItems ?? null;
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

        $result['userInfo'] = [
            'firstName' => $natural->first_name,
            'lastName' => $natural->last_name,
            'avatar' => !is_null($user->person->avatar) ? $user->person->avatar->slug : null,
            'roles' => $user->roles,
        ];

        return response()->json($result)->withCookie($cookie);
    }

}
