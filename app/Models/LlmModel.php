<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LlmModel extends Model {
    protected $primaryKey = 'llm_model_id';
    protected $fillable = ['model_name', 'upstream'];
}
