<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable {
    use HasFactory, Notifiable;
    protected $fillable = ['name', 'email', 'password', 'apitoken', 'sub_id', 'active', 'expires_at'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'active' => 'boolean', 'expires_at' => 'datetime'];
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'sub_id');
    }
}
