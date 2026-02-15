<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LlmModel extends Model {
    protected $primaryKey = 'llm_model_id';
    protected $fillable = ['model_name', 'upstream', 'provider_id'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
