<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    // protected function redirectTo($request)
    // {
    //     if (! $request->expectsJson()) {
    //         return [
    //             'status'=>true,
    //             'message'=>'Unauthenticated'
    //         ];
    //     }
    // }

    public function handle($request, Closure $next, ...$guards)
    {
        if(!auth()->guard('sanctum')->check()){
            return \response()->json([
                    'status'=>true,
                    'message'=>'Unauthenticated'
                ]);
        }
        return $next($request);
    }
}
