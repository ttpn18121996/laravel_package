<?php

namespace PhuongNam\UserAndPermission\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Middleware\CheckCredentials;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class CheckClientCredentials extends CheckCredentials
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$scopes
     * @return mixed
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        $psr = (new PsrHttpFactory(
            new Psr17Factory,
            new Psr17Factory,
            new Psr17Factory,
            new Psr17Factory
        ))->createRequest($request);

        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);
        } catch (OAuthServerException $e) {
            return nRes()->res401($e->getMessage());
        }

        $this->validate($psr, $scopes);

        return $next($request);
    }

    protected function validateCredentials($token) {}
    protected function validateScopes($token, $scopes) {}
}
