<?php
// Testing GitHub Actions Workflow
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UsageLog extends Model {
    protected $primaryKey = 'usage_id';
    public $timestamps = false;
    protected $fillable = ['quota_item_id', 'user_id', 'requested_model', 'routed_model', 'upstream_provider', 'token_count'];
}
