<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;

/**
 * Tracking ID: attached to every log entry (Context) and returned to the client.
 */
class AssignRequestId
{
    public function handle(Request $request, Closure $next)
    {
        Context::add('request_id', $request->header('X-Request-Id') ?: (string) Str::uuid());

        $response = $next($request);
        $response->headers->set('X-Request-Id', Context::get('request_id'));

        return $response;
    }
}
