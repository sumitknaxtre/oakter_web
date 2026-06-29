<?php

namespace App\Services\Meta;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends server-side events to Meta Conversions API (Graph API).
 */
class MetaConversionApiService
{
    public function isEnabled(): bool
    {
        return config('meta.enable_capi')
            && is_string(config('meta.pixel_id'))
            && config('meta.pixel_id') !== ''
            && is_string(config('meta.access_token'))
            && config('meta.access_token') !== '';
    }

    /**
     * @param  array<string, mixed>  $event
     */
    public function sendEvent(array $event): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $pixelId = config('meta.pixel_id');
        $version = config('meta.api_version', 'v21.0');
        $url = sprintf('https://graph.facebook.com/%s/%s/events', $version, $pixelId);

        $payload = [
            'data' => [$event],
            'access_token' => config('meta.access_token'),
        ];

        $testEventCode = config('meta.test_event_code');

        if (is_string($testEventCode) && $testEventCode !== '') {
            $payload['test_event_code'] = $testEventCode;
        }

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->post($url, $payload);

            if (app()->environment('local')) {
                Log::info('Meta CAPI request sent.', [
                    'url' => $url,
                    'event_name' => $event['event_name'] ?? null,
                    'event_id' => $event['event_id'] ?? null,
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ]);
            }

            if ($response->failed()) {
                Log::warning('Meta CAPI request failed.', [
                    'event_name' => $event['event_name'] ?? null,
                    'event_id' => $event['event_id'] ?? null,
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $exception) {
            Log::warning('Meta CAPI request threw an exception.', [
                'event_name' => $event['event_name'] ?? null,
                'event_id' => $event['event_id'] ?? null,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
