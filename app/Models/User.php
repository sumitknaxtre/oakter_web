<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['first_name', 'last_name', 'email', 'phone', 'password', 'role_id', 'admin_permissions'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === Role::ADMIN;
    }

    public function isSubAdmin(): bool
    {
        return $this->role?->name === Role::SUB_ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isSubAdmin();
    }

    public function hasAdminPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->isSubAdmin()) {
            return false;
        }

        return in_array($permission, $this->admin_permissions ?? [], true);
    }

    public function isCustomer(): bool
    {
        return $this->role?->name === Role::CUSTOMER;
    }

    protected function name(): Attribute
    {
        return Attribute::get(fn () => trim($this->first_name.' '.$this->last_name));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'admin_permissions' => 'array',
        ];
    }
}
