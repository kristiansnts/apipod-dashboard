<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LlmModel extends Model
{
    protected $primaryKey = 'llm_model_id';
    protected $fillable = [
        'model_name',
        'provider_id',
        'input_cost_per_1m',
        'output_cost_per_1m',
        'rpm',
        'tpm',
        'rpd',
        'tool_support',
        'max_context',
        'default_weight',
    ];

    protected function casts(): array
    {
        return [
            'input_cost_per_1m' => 'decimal:4',
            'output_cost_per_1m' => 'decimal:4',
            'tool_support' => 'boolean',
            'max_context' => 'integer',
            'default_weight' => 'integer',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Plans that include this model.
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_model', 'llm_model_id', 'plan_id');
    }
}
