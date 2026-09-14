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
     * Full list for admin create/edit: official states/UTs plus CSV territory labels.
     *
     * @return list<string>
     */
    public static function adminRegionOptions(): array
    {
        $official = config('india.states', []);
        $csvTerritories = config('dealer_regions', []);

        $merged = array_unique([
            ...(is_array($official) ? $official : []),
            ...(is_array($csvTerritories) ? $csvTerritories : []),
        ]);

        sort($merged, SORT_STRING);

        return array_values($merged);
    }

    /**
     * Regions with at least one active dealer (website dropdown), alphabetically.
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

        sort($statesWithDealers, SORT_STRING);

        return array_values($statesWithDealers);
    }
}
