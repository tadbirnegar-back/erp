<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use League\OAuth2\Server\Exception\OAuthServerException;
use Throwable;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueOAuthException;
use Laravel\Passport\Exceptions\OAuthServerException as PassportOAuthException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected $dontReport = [
        LeagueOAuthException::class,
        PassportOAuthException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function report(Throwable $e)
    {
        // Don’t log “access denied” OAuth errors
        if ($e instanceof OAuthServerException
            && $e->getErrorType() === 'access_denied'
        ) {
            return;
        }

        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        // Turn it into a neat 401 JSON response, rather than a 500 stack
        if ($e instanceof OAuthServerException) {
            return response()->json([
                'error' => 'Unauthorized',
                'error_description' => $e->getMessage(),
            ], $e->getHttpStatusCode());
        }

        return parent::render($request, $e);
    }
}
