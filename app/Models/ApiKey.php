<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'org_id',
        'name',
        'key_hash',
        'key_prefix',
        'token_limit',
        'used_tokens',
        'is_active',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'token_limit' => 'integer',
            'used_tokens' => 'integer',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    /**
     * Generate a new API key, store the hash, return the plain key.
     */
    public static function generateKey(int $orgId, string $name, ?int $tokenLimit = null): array
    {
        $plainKey = 'apipod_' . Str::random(40);
        $prefix = substr($plainKey, 0, 12);

        $apiKey = static::create([
            'org_id' => $orgId,
            'name' => $name,
            'key_hash' => hash('sha256', $plainKey),
            'key_prefix' => $prefix,
            'token_limit' => $tokenLimit,
        ]);

        // Return both — plain key is shown ONCE to user, never again
        return ['api_key' => $apiKey, 'plain_key' => $plainKey];
    }

    /**
     * Find an API key by its plain text value.
     */
    public static function findByPlainKey(string $plainKey): ?static
    {
        $hash = hash('sha256', $plainKey);
        return static::where('key_hash', $hash)->first();
    }

    /**
     * Check if this key has exceeded its per-key token limit.
     */
    public function isWithinLimit(int $additionalTokens = 0): bool
    {
        if ($this->token_limit === null) {
            return true; // no limit set
        }

        return ($this->used_tokens + $additionalTokens) <= $this->token_limit;
    }
}
