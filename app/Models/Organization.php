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
        'daily_request_count',
        'daily_request_date',
        'active_model_id',
    ];

    protected function casts(): array
    {
        return [
            'token_balance' => 'integer',
            'quota_reset_at' => 'datetime',
            'is_active' => 'boolean',
            'next_billing_at' => 'datetime',
            'daily_request_count' => 'integer',
            'daily_request_date' => 'date',
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

    public function providerKeys(): HasMany
    {
        return $this->hasMany(OrgProviderKey::class, 'org_id');
    }

    public function activeModel(): BelongsTo
    {
        return $this->belongsTo(LlmModel::class, 'active_model_id', 'llm_model_id');
    }

    /**
     * Check if org has token balance remaining.
     */
    public function hasTokenBalance(): bool
    {
        return $this->token_balance > 0;
    }
}
