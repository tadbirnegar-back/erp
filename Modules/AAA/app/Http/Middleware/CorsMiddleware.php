<?php

namespace Modules\AAA\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $origin = $request->header('Origin');

        $response->headers->set('Access-Control-Allow-Credentials', 'true');
//        $response->headers->set('Cache-Control', 'public, max-age=86400');

        // Check if the Access-Control-Allow-Origin header has already been set
        if ($response->headers->has('Access-Control-Allow-Origin')) {
            $response->headers->remove('Access-Control-Allow-Origin');
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }else{
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));

        return $response;
    }
}
