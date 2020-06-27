<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{

    protected $guard;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Log::info(
            "Customer ",
            array('request' => $this->auth->user())
        );
        if ($this->auth->user()->user_role_id != 1) {
            abort("403", "Unauthorized action.");
        }

        return $next($request);
    }
}
