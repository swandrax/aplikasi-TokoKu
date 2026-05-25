<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'otp_code',
        'otp_expires_at',
        'email_verified_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir' || $this->role === 'admin';
    }

    public function isPembeli(): bool
    {
        return $this->role === 'pembeli';
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'created_by');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class, 'created_by');
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class, 'created_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function cashierOrders()
    {
        return $this->hasMany(Order::class, 'kasir_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    public function notificationsLog()
    {
        return $this->hasMany(NotificationLog::class, 'user_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function chatbotSessions()
    {
        return $this->hasMany(ChatbotSession::class, 'user_id');
    }
}
