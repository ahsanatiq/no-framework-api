<?php
namespace App\Middlewares;

use Illuminate\Http\Request;
use Closure;

class JsonHeaderMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        $response = $next($request);
        $response->headers->set('content-type', 'application/json');
        return $response;
    }
}
