<?php

namespace App\Support;

use App\Models\User;

class AdminPermissions
{
    public const DASHBOARD = 'dashboard';

    public const ORDERS = 'orders';

    public const PRODUCTS = 'products';

    public const COUPONS = 'coupons';

    public const CUSTOMERS = 'customers';

    /**
     * @return array<string, string>
     */
    public static function sidebarOptions(): array
    {
        return [
            self::DASHBOARD => 'Dashboard',
            self::ORDERS => 'Orders',
            self::CUSTOMERS => 'Customers',
            self::PRODUCTS => 'Products',
            self::COUPONS => 'Coupons',
        ];
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::sidebarOptions());
    }

    /**
     * @return array<string, string>
     */
    public static function routeMap(): array
    {
        return [
            'admin.dashboard' => self::DASHBOARD,
            'admin.orders.*' => self::ORDERS,
            'admin.abandoned-orders.*' => self::ORDERS,
            'admin.customers.*' => self::CUSTOMERS,
            'admin.products.*' => self::PRODUCTS,
            'admin.coupons.*' => self::COUPONS,
        ];
    }

    public static function permissionForRoute(?string $routeName): ?string
    {
        if ($routeName === null) {
            return null;
        }

        foreach (self::routeMap() as $pattern => $permission) {
            if (str_ends_with($pattern, '.*')) {
                $prefix = str_replace('.*', '', $pattern);

                if (str_starts_with($routeName, $prefix)) {
                    return $permission;
                }

                continue;
            }

            if ($routeName === $pattern) {
                return $permission;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public static function normalize(array $permissions): array
    {
        return array_values(array_intersect($permissions, self::keys()));
    }

    public static function landingRouteFor(User $user): string
    {
        foreach (self::routeMap() as $pattern => $permission) {
            if (! $user->hasAdminPermission($permission)) {
                continue;
            }

            if (str_ends_with($pattern, '.*')) {
                $routeName = str_replace('.*', '.index', $pattern);

                if (\Illuminate\Support\Facades\Route::has($routeName)) {
                    return route($routeName);
                }
            }

            if (\Illuminate\Support\Facades\Route::has($pattern)) {
                return route($pattern);
            }
        }

        return route('admin.profile.edit');
    }
}
