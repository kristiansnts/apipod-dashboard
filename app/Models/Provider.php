<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'base_url', 'provider_type', 'api_key', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function llmModels()
    {
        return $this->hasMany(LlmModel::class);
    }

    public function providerAccounts()
    {
        return $this->hasMany(ProviderAccount::class);
    }
}
