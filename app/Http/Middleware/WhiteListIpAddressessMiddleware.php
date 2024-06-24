<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WhiteListIpAddressessMiddleware
{
    /**
     * @var string[]
     */
    public $whitelistIps = [

        // Nagad
        '172.70.142.23',
        '172.70.92.181',
        //Staging:
        '103.147.110.117',
        '103.147.110.119',
        '103.147.110.0',

 
        //Production:
        '103.147.110.118',
        '103.147.110.76',

        //DBBL-Rocket
        //Staging:
        '103.11.137.17',
        '103.11.136.153'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->getClientIp(), $this->whitelistIps)) {
            abort(403, "You are restricted to access the site." .$request->getClientIp());
        }

        return $next($request);
    }
}
