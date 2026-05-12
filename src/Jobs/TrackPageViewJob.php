<?php

namespace Kholil\FilamentAnalitik\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kholil\FilamentAnalitik\Models\PageView;
use Stevebauman\Location\Facades\Location;

class TrackPageViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $data
    ) {}

    public function handle(): void
    {
        $location = null;
        $ip = $this->data['ip'];

        // For local development testing, if IP is localhost, use a dummy public IP
        if (($ip === '127.0.0.1' || $ip === '::1') && config('app.env') === 'local') {
            $ip = '8.8.8.8'; // Google DNS IP for testing location
        }
        
        if (!empty($ip)) {
            $location = Location::get($ip);
        }

        PageView::create([
            'url' => $this->data['url'],
            'path' => $this->data['path'],
            'method' => $this->data['method'],
            'ip' => $this->data['ip'],
            'user_agent' => $this->data['user_agent'],
            'city' => $location?->cityName,
            'state' => $location?->regionName,
            'country' => $location?->countryName,
            'project_id' => $this->data['project_id'] ?? null,
        ]);
    }
}
