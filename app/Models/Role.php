<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const ADMIN = 'admin';

    public const SUB_ADMIN = 'sub_admin';

    public const CUSTOMER = 'customer';

    protected $fillable = [
        'name',
        'label',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
