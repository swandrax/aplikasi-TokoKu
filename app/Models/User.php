<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_USER_ADMIN = '0';
    public const ROLE_ADMIN = '1';
    public const ROLE_CUSTOMER = '2';

    protected $table = "user";

    protected $fillable = [
        'nama', 'email', 'role', 'status', 'password', 'hp', 'foto',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUserAdmin(): bool
    {
        return $this->role === self::ROLE_USER_ADMIN;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array((string) $this->role, array_map('strval', $roles), true);
    }

    public function roleLabel(): string
    {
        return match ((string) $this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_USER_ADMIN => 'User Admin',
            self::ROLE_CUSTOMER => 'Customer',
            default => 'Tidak Dikenal',
        };
    }
}
