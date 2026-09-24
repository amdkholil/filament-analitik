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

        if (! config('filament-analitik.enabled', true)) {
            return $response;
        }

        // Exclude Filament panel pages (route-based is Octane-safe; current panel as fallback)
        if ($request->routeIs('filament.*') || Filament::getCurrentPanel()) {
            return $response;
        }

        // Only track successful GET requests
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return $response;
        }

        if ($this->isBot($request)) {
            return $response;
        }

        $path = $request->path();
        $path = '/' . ltrim($path === '' ? '/' : $path, '/');

        TrackPageViewJob::dispatch([
            'url' => $request->fullUrl(),
            'path' => $path,
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'project_id' => config('filament-analitik.project_id'),
        ]);

        return $response;
    }

    protected function isBot(Request $request): bool
    {
        if (! config('filament-analitik.exclude_bots', true)) {
            return false;
        }

        $userAgent = (string) $request->userAgent();

        if ($userAgent === '') {
            return true;
        }

        $patterns = config('filament-analitik.bot_patterns', []);

        foreach ($patterns as $pattern) {
            if ($pattern !== '' && stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
