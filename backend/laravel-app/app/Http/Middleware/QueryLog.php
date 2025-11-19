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
        $channel = app()->environment('testing') ? 'query-testing' : 'query';

        $routeURI = $request->uri() ?? 'N/A';
        $method = $request->method();

        Log::channel($channel)->info('-----------------------------------------------------------');
        Log::channel($channel)->info('URI: ' . $method . ' ' . $routeURI);

        DB::listen(function (QueryExecuted $query) use ($channel) {
            Log::channel($channel)->info('');
            Log::channel($channel)->info('Query: ' . $query->sql);
            Log::channel($channel)->info('Bindings: ', $query->bindings);
            Log::channel($channel)->info('Time (miliseconds): ' . $query->time . ' ms');
        });

        return $next($request);
    }
}
