<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'plan_id',
        'token_balance',
        'quota_reset_at',
        'is_active',
        'next_billing_at',
    ];

    protected function casts(): array
    {
        return [
            'token_balance' => 'integer',
            'quota_reset_at' => 'datetime',
            'is_active' => 'boolean',
            'next_billing_at' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'org_id');
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class, 'org_id');
    }

    public function tokenLedger(): HasMany
    {
        return $this->hasMany(TokenLedger::class, 'org_id');
    }

    /**
     * Check if org can create more API keys based on plan limit.
     */
    public function canCreateApiKey(): bool
    {
        if (!$this->plan) {
            return false;
        }

        return $this->apiKeys()->count() < $this->plan->max_api_keys;
    }

    /**
     * Check if org has token balance remaining.
     */
    public function hasTokenBalance(): bool
    {
        return $this->token_balance > 0;
    }
}
