<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenLedger extends Model
{
    public $timestamps = false;

    protected $table = 'token_ledger';

    protected $fillable = [
        'org_id',
        'user_id',
        'api_key_id',
        'request_id',
        'type',
        'model',
        'input_tokens',
        'output_tokens',
        'cost_usd',
        'balance_after',
        'description',
        'status_code',
        'latency_ms',
        'cache_hit',
    ];

    protected function casts(): array
    {
        return [
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'cost_usd' => 'decimal:6',
            'balance_after' => 'integer',
            'status_code' => 'integer',
            'latency_ms' => 'integer',
            'cache_hit' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    /**
     * Total tokens for this entry.
     */
    public function getTotalTokensAttribute(): int
    {
        return $this->input_tokens + $this->output_tokens;
    }
}
