<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'base_url', 'provider_type', 'api_key'];

    public function llmModels()
    {
        return $this->hasMany(LlmModel::class);
    }

    public function providerAccounts()
    {
        return $this->hasMany(ProviderAccount::class);
    }
}
