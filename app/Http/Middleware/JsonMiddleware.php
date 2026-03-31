<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Json;
class JsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request The request
     * @param \Closure $next The next middleware
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set the Accept header to application/json
        $request->headers->set('Accept', 'application/json');

        // Proceed to the next middleware or controller action
        $response = $next($request);

        // Ensure the Content-Type header is set to application/json
        $response->headers->set('Content-Type', 'application/json');

       return $response;
    }

}