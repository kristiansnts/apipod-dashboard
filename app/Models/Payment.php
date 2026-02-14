<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
    protected $fillable = ['user_id', 'external_id', 'amount', 'status', 'payment_method', 'paid_at'];
}
