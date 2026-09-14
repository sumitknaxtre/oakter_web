<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'map_url',
        'state',
        'district',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<Dealer>  $query
     * @return Builder<Dealer>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Dealer>  $query
     * @return Builder<Dealer>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('district')
            ->orderBy('name');
    }

    /**
     * Territory region labels available for dealers (CSV order / alphabetical config).
     *
     * @return list<string>
     */
    public static function regionOptions(): array
    {
        $configured = config('dealer_regions');

        if (is_array($configured) && $configured !== []) {
            return array_values($configured);
        }

        return self::query()
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->all();
    }

    /**
     * Territory regions that have at least one active dealer, in config order.
     *
     * @return list<string>
     */
    public static function activeStates(): array
    {
        $statesWithDealers = self::query()
            ->active()
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->pluck('state')
            ->all();

        $regions = self::regionOptions();

        $ordered = array_values(array_filter(
            $regions,
            fn (string $state): bool => in_array($state, $statesWithDealers, true),
        ));

        if ($ordered !== []) {
            return $ordered;
        }

        sort($statesWithDealers);

        return array_values($statesWithDealers);
    }
}
