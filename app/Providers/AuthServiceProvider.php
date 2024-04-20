<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Modules\AAA\app\Auth\OTPGrant;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
//        $this->registerPolicies();

        app(AuthorizationServer::class)->enableGrantType(
            $this->makeOtpGrant(), Passport::tokensExpireIn()
        );

//        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(2));
        Passport::refreshTokensExpireIn(now()->addHours(6));
        Passport::personalAccessTokensExpireIn(now()->addHours(6));
    }
    protected function makeOtpGrant()
    {
        $grant = new OtpGrant(
            app(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
