<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LlmModel extends Model {
    protected $primaryKey = 'llm_model_id';
    protected $fillable = [
        'model_name',
        'provider_id',
        'input_cost_per_1m',
        'output_cost_per_1m',
        'rpm',
        'tpm',
        'rpd'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
