<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
    protected $fillable = ['order_code', 'user_id', 'status', 'total_amount', 'cash_received', 'change_amount'];
    public function user() { return $this->belongsTo(User::class); }
    public function details() { return $this->hasMany(OrderDetail::class); }
}