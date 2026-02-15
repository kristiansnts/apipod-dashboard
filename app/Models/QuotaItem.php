<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class QuotaItem extends Model {
    protected $primaryKey = 'quota_id';
    protected $fillable = ['sub_id', 'llm_model_id', 'percentage_weight', 'token_limit', 'rpm_limit'];
    public function llmModel() { return $this->belongsTo(LlmModel::class, 'llm_model_id'); }
}
