<?php
// Testing GitHub Actions Workflow
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageLog extends Model {
    protected $primaryKey = 'usage_id';
    public $timestamps = false;
    protected $fillable = ['quota_item_id', 'user_id', 'requested_model', 'routed_model', 'upstream_provider', 'status', 'token_count', 'input_tokens', 'output_tokens'];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quotaItem(): BelongsTo
    {
        return $this->belongsTo(QuotaItem::class, 'quota_item_id');
    }
}
