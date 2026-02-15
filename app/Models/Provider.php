<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'base_url', 'api_key', 'provider_type'];

    public function llmModels()
    {
        return $this->hasMany(LlmModel::class);
    }
}
