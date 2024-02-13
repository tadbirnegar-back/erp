<?php

namespace Modules\AAA\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class CheckRoutePermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();  // Get the authenticated user
        $requestedRoute = $request->route()->uri(); // Get the requested route name
        $requestedRoute=str_replace('api/v1', '', $requestedRoute);
        $requestedRoute=str_replace('\\', '', $requestedRoute);
//        return response()->json($user->hasPermissionForRoute($requestedRoute));
        // Check if the user has permission for the route
        if ($user->hasPermissionForRoute($requestedRoute)) {

            return $next($request); // Allow access
        }

        // Handle unauthorized access (e.g., redirect, display error message)
        return response()->json(['شما به این بخش دسترسی ندارید'],403); // Example redirect
    }
}
