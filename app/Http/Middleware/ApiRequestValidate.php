<?php

namespace App\Http\Middleware;

use App\Models\ApiRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ApiRequestValidate {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        $lastMinute = Carbon::now()->subMinutes()->toDateTimeString();
        $countQueriesLastMinute = ApiRequest::where('created_at', '>=', $lastMinute)->count();
        if ($countQueriesLastMinute >= 3) {
            return response()->json('No puedes realizar más consultas', 401);
        }
        ApiRequest::create([
            'token' => $request->user()->currentAccessToken()->token,
            'path' => $request->fullUrl(),
            'method' => $request->method()
        ]);
        return $next($request);
    }
}
