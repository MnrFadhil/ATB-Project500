<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSensorApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-API-KEY');

        if (!$key || $key !== config('services.sensor_api_key')) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        return $next($request);
    }
}
