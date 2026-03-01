<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrgProviderKey extends Model
{
    protected $fillable = [
        'org_id',
        'provider_id',
        'api_key',
        'label',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Masked display: "sk-abc...xyz" (first 6 + last 4).
     * Never show full key in UI or admin.
     */
    public function getMaskedKeyAttribute(): string
    {
        $plain = $this->api_key;
        if (strlen($plain) <= 10) {
            return '••••••••';
        }
        return substr($plain, 0, 6) . '...' . substr($plain, -4);
    }

    /**
     * Deactivate all provider keys for an org (on plan downgrade).
     */
    public static function deactivateForOrg(int $orgId): void
    {
        static::where('org_id', $orgId)->update(['is_active' => false]);
    }

    /**
     * Reactivate all provider keys for an org (on upgrade back to BYOK).
     */
    public static function reactivateForOrg(int $orgId): void
    {
        static::where('org_id', $orgId)->update(['is_active' => true]);
    }
}
