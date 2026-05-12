<?php

namespace Kholil\FilamentAnalitik\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Kholil\FilamentAnalitik\Jobs\TrackPageViewJob;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!config('filament-analitik.enabled', true)) {
            return $response;
        }

        // Exclude Filament panel pages
        if (Filament::getCurrentPanel()) {
            return $response;
        }

        // Only track successful GET requests
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            TrackPageViewJob::dispatch([
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'project_id' => config('filament-analitik.project_id'),
            ]);
        }

        return $response;
    }
}
