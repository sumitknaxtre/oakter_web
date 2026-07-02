<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * First-touch marketing attribution captured in session and stored on orders.
 */
final class OrderAttribution
{
    public const SESSION_KEY = 'marketing_attribution';

    /**
     * @return array<string, mixed>
     */
    public static function captureFromRequest(Request $request): array
    {
        $data = [
            'utm_source' => self::clean($request->query('utm_source')),
            'utm_medium' => self::clean($request->query('utm_medium')),
            'utm_campaign' => self::clean($request->query('utm_campaign')),
            'utm_content' => self::clean($request->query('utm_content')),
            'utm_term' => self::clean($request->query('utm_term')),
            'referrer' => self::clean($request->headers->get('referer')),
            'landing_url' => $request->fullUrl(),
            'captured_at' => now()->toIso8601String(),
        ];

        return array_filter($data, fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @param  array<string, mixed>  $attribution
     * @return array<string, mixed>
     */
    public static function withMetaCookies(array $attribution, ?string $fbp, ?string $fbc): array
    {
        if (is_string($fbp) && $fbp !== '') {
            $attribution['fbp'] = $fbp;
        }

        if (is_string($fbc) && $fbc !== '') {
            $attribution['fbc'] = $fbc;
        }

        return $attribution;
    }

    /**
     * @param  array<string, mixed>|null  $attribution
     */
    public static function label(?array $attribution): string
    {
        if ($attribution === null || $attribution === []) {
            return 'Direct / unknown';
        }

        if (! empty($attribution['fbc'])) {
            return 'Meta ad click';
        }

        $source = strtolower((string) ($attribution['utm_source'] ?? ''));

        if ($source !== '') {
            if (str_contains($source, 'instagram')) {
                return 'Instagram (UTM)';
            }

            if (str_contains($source, 'facebook') || $source === 'fb' || str_contains($source, 'meta')) {
                return 'Facebook / Meta (UTM)';
            }

            return 'UTM: '.$attribution['utm_source'];
        }

        $referrer = strtolower((string) ($attribution['referrer'] ?? ''));

        if (str_contains($referrer, 'instagram.com')) {
            return 'Instagram (referrer)';
        }

        if (str_contains($referrer, 'facebook.com') || str_contains($referrer, 'fb.com')) {
            return 'Facebook (referrer)';
        }

        if ($referrer !== '') {
            $host = parse_url($attribution['referrer'], PHP_URL_HOST);

            return 'Referrer: '.($host ?: $attribution['referrer']);
        }

        return 'Direct / unknown';
    }

    /**
     * @param  array<string, mixed>|null  $attribution
     * @return list<array{label: string, value: string}>
     */
    public static function detailRows(?array $attribution): array
    {
        if ($attribution === null || $attribution === []) {
            return [];
        }

        $rows = [
            ['label' => 'Channel', 'value' => self::label($attribution)],
        ];

        $fields = [
            'utm_source' => 'UTM source',
            'utm_medium' => 'UTM medium',
            'utm_campaign' => 'UTM campaign',
            'utm_content' => 'UTM content',
            'utm_term' => 'UTM term',
            'referrer' => 'Referrer',
            'landing_url' => 'Landing URL',
            'fbp' => 'Meta browser ID (_fbp)',
            'fbc' => 'Meta click ID (_fbc)',
            'captured_at' => 'First visit captured',
        ];

        foreach ($fields as $key => $label) {
            if (! empty($attribution[$key])) {
                $rows[] = [
                    'label' => $label,
                    'value' => (string) $attribution[$key],
                    'breakable' => in_array($key, ['landing_url', 'referrer', 'fbp', 'fbc'], true),
                ];
            }
        }

        return $rows;
    }

    private static function clean(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
