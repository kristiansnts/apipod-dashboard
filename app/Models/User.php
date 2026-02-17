<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable {
    use HasFactory, Notifiable;
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'apitoken', 
        'sub_id', 
        'active', 
        'expires_at', 
        'provider_name', 
        'provider_id',
        'tokens_used',
        'quota_reset_at'
    ];
    protected $hidden = ['password', 'remember_token'];
    
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime', 
            'password' => 'hashed', 
            'active' => 'boolean', 
            'expires_at' => 'datetime',
            'quota_reset_at' => 'datetime'
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'sub_id');
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(UsageLog::class, 'user_id');
    }
}
