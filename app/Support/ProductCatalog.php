<?php

namespace App\Support;

use App\Models\Product;
use InvalidArgumentException;

class ProductCatalog
{
    public static function slugs(): array
    {
        return [
            'studio-ac' => 'studio_ac',
            'mini-ups' => 'mini_ups',
            'mini-ups-airfiber' => 'mini_ups_airfiber',
            'gan-charger' => 'gan_charger',
        ];
    }

    public static function buyRouteNames(): array
    {
        return [
            'studio-ac' => 'website.buy_studio_ac',
            'mini-ups' => 'website.buy_mini_ups',
            'mini-ups-airfiber' => 'website.buy_mini_ups_airfiber',
            'gan-charger' => 'website.buy_gan_charger',
        ];
    }

    public static function buyRoute(string $slug): string
    {
        $routeName = self::buyRouteNames()[$slug] ?? null;

        if ($routeName === null) {
            throw new InvalidArgumentException("Unknown product slug [{$slug}].");
        }

        return route($routeName);
    }

    public static function slugForKey(string $key): string
    {
        $slug = array_search($key, self::slugs(), true);

        if ($slug === false) {
            throw new InvalidArgumentException("Unknown product key [{$key}].");
        }

        return $slug;
    }

    public static function keyFromSlug(string $slug): string
    {
        $key = self::slugs()[$slug] ?? null;

        if ($key === null) {
            throw new InvalidArgumentException("Unknown product slug [{$slug}].");
        }

        return $key;
    }

    public static function forConfigKey(string $configKey): array
    {
        $product = Product::query()->where('config_key', $configKey)->first();

        if ($product === null) {
            $config = config("products.{$configKey}");

            if ($config === null) {
                throw new InvalidArgumentException("Product config missing for [{$configKey}].");
            }

            return array_merge($config, [
                'is_in_stock' => true,
                'hide_buy_button' => false,
            ]);
        }

        return $product->toCatalogArray();
    }

    public static function get(string $slug): array
    {
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->first();

        if ($product === null) {
            throw new InvalidArgumentException("Product not found for slug [{$slug}].");
        }

        return $product->toCheckoutArray();
    }
}
