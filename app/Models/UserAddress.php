<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'first_name',
        'last_name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'pincode',
        'country',
        'is_default',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toSnapshot(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'country' => $this->country,
        ];
    }

    public function toFormArray(): array
    {
        return $this->toSnapshot();
    }
}
