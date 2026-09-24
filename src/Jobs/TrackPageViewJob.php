<?php

namespace Kholil\FilamentAnalitik\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kholil\FilamentAnalitik\Models\PageView;
use Stevebauman\Location\Facades\Location;
use Throwable;

class TrackPageViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public array $data
    ) {}

    public function handle(): void
    {
        try {
            $location = null;
            $ip = $this->data['ip'] ?? null;

            // For local development testing, if IP is localhost, use a dummy public IP
            if (($ip === '127.0.0.1' || $ip === '::1') && config('app.env') === 'local') {
                $ip = '8.8.8.8';
            }

            if (! empty($ip)) {
                try {
                    $location = Location::get($ip);
                    if ($location === false) {
                        $location = null;
                    }
                } catch (Throwable) {
                    $location = null;
                }
            }

            PageView::create([
                'url' => $this->data['url'] ?? '',
                'path' => $this->data['path'] ?? '/',
                'method' => $this->data['method'] ?? 'GET',
                'ip' => $this->data['ip'] ?? null,
                'user_agent' => $this->data['user_agent'] ?? null,
                'city' => $location?->cityName,
                'state' => $location?->regionName,
                'country' => $location?->countryName,
                'project_id' => $this->data['project_id'] ?? null,
            ]);
        } catch (Throwable $e) {
            // Fail-safe (SRS FR-4.2): tracking must never break the visitor request.
            report($e);
        }
    }

    public function failed(?Throwable $exception): void
    {
        report($exception ?? new \RuntimeException('TrackPageViewJob failed.'));
    }
}
