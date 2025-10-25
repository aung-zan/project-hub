<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class QueryLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeURI = $request->uri() ?? 'N/A';
        $method = $request->method();

        Log::channel('query')->info('-----------------------------------------------------------');
        Log::channel('query')->info('URI: ' . $method . ' ' . $routeURI);

        DB::listen(function (QueryExecuted $query) {
            Log::channel('query')->info('');
            Log::channel('query')->info('Query: ' . $query->sql);
            Log::channel('query')->info('Bindings: ', $query->bindings);
            Log::channel('query')->info('Time (miliseconds): ' . $query->time . ' ms');
        });

        return $next($request);
    }
}
