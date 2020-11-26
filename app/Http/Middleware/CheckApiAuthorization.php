<?php

namespace App\Http\Middleware;

use Closure;
use App\Classes\Authorization\AuthorizationTokenGetter;

class CheckApiAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authorizationClass = new AuthorizationTokenGetter;

        if ($authorizationClass->getBearerToken() != $authorizationClass->getFixedAuthToken())
        {
            return response()->json([
                "status" =>  "error",
                "code" => 403,
                "message" => 'Invalid token',
            ]);
        }

        return $next($request);
    }
}
