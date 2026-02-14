<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Subscription extends Model {
    protected $primaryKey = 'sub_id';
    protected $fillable = ['sub_name', 'price', 'system_prompt'];
}
