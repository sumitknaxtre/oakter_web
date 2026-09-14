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
     * Territory regions that have at least one active dealer, in config order.
     *
     * @return list<string>
     */
    public static function activeStates(): array
    {
        $statesWithDealers = self::query()
            ->active()
            ->distinct()
            ->pluck('state')
            ->all();

        return array_values(array_filter(
            config('dealer_regions'),
            fn (string $state): bool => in_array($state, $statesWithDealers, true),
        ));
    }
}
