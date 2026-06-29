<?php

namespace App\Services\Meta;

/**
 * Normalizes and SHA-256-hashes customer data per Meta Conversions API requirements.
 *
 * @see https://developers.facebook.com/docs/marketing-api/conversions-api/parameters/customer-information-parameters
 */
class MetaHasher
{
    public static function hash(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return hash('sha256', $value);
    }

    public static function email(?string $email): ?string
    {
        if ($email === null || trim($email) === '') {
            return null;
        }

        return self::hash(strtolower(trim($email)));
    }

    public static function phone(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        // Indian numbers: store as 91 + 10-digit mobile when possible.
        if (strlen($digits) === 10) {
            $digits = '91'.$digits;
        }

        return self::hash($digits);
    }

    public static function name(?string $name): ?string
    {
        if ($name === null || trim($name) === '') {
            return null;
        }

        $normalized = strtolower(preg_replace('/[^a-z]/i', '', $name) ?? '');

        return $normalized === '' ? null : self::hash($normalized);
    }

    public static function city(?string $city): ?string
    {
        return self::hash(self::normalizeLocation($city));
    }

    public static function state(?string $state): ?string
    {
        return self::hash(self::normalizeLocation($state));
    }

    public static function zip(?string $zip): ?string
    {
        if ($zip === null || trim($zip) === '') {
            return null;
        }

        $normalized = strtolower(preg_replace('/[\s-]+/', '', trim($zip)) ?? '');

        return $normalized === '' ? null : self::hash($normalized);
    }

    public static function country(?string $country): ?string
    {
        if ($country === null || trim($country) === '') {
            return null;
        }

        $normalized = strtolower(trim($country));

        if ($normalized === 'india') {
            $normalized = 'in';
        }

        if (strlen($normalized) !== 2) {
            return null;
        }

        return self::hash($normalized);
    }

    private static function normalizeLocation(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $normalized = strtolower(preg_replace('/[^a-z]/i', '', $value) ?? '');

        return $normalized === '' ? null : $normalized;
    }
}
