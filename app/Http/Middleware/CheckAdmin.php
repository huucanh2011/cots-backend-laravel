<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdmin
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
        // $userRoles = auth()->user()->role->pluck('role_name');
        // if(!$userRoles->contains('admin')) {
        //     return response()->json([
        //         'error' => 'Khong du quyen'
        //     ]);
        // }

        if(auth()->user()->role_id !== 1) {
            return response()->json([
                'error' => 'khong du quyen'
            ]);
        }

        return $next($request);
    }
}
