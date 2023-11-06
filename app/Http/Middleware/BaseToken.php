<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Firebase\JWT\{ JWT,Key };
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class BaseToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   

        $key = $request->header(config('sanctum.api_auth_key'));

        if (empty($key)) {
            throw new UnauthorizedHttpException('Guard', 'The supplied API KEY is missing or an invalid authorization header was sent');
        }

        $secret = config('sanctum.api_auth_secret_key');
        JWT::$leeway = 60;
        $decodedData = JWT::decode($key, new Key($secret, 'HS256'));

        if ($decodedData->url != $request->url()) {
            throw new UnauthorizedHttpException('Guard', 'The supplied API KEY is missing or an invalid authorization header was sent');
        }

        return $next($request);
    }
}
