<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'sub_id',
        'duration_days',
        'is_active',
        'token_quota',
        'max_api_keys',
        'rate_limit_rpm',
        'rate_limit_tpm',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'duration_days' => 'integer',
            'token_quota' => 'integer',
            'max_api_keys' => 'integer',
            'rate_limit_rpm' => 'integer',
            'rate_limit_tpm' => 'integer',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'sub_id', 'sub_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * Models allowed for this plan.
     */
    public function allowedModels(): BelongsToMany
    {
        return $this->belongsToMany(LlmModel::class, 'plan_model', 'plan_id', 'llm_model_id');
    }

    /**
     * Check if a model is allowed by this plan.
     */
    public function isModelAllowed(string $modelName): bool
    {
        return $this->allowedModels()->where('model_name', $modelName)->exists();
    }
}
