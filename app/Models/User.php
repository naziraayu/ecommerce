<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'address',
        'phone_number'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ===== RELATIONS =====

    public function roleData(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }
    
    // ===== ROLE HELPERS =====

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->roleData?->name === 'admin';
    }

    // public function isSuperAdmin(): bool
    // {
    //     return $this->role === 'super_admin' || $this->roleData?->name === 'super_admin';
    // }

    public function getRoleNameAttribute(): string
    {
        return $this->roleData?->name ?? $this->role ?? 'user';
    }

}