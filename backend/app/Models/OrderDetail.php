<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderDetail extends Model {
    protected $fillable = ['order_id', 'book_id', 'qty', 'unit_price', 'subtotal'];
    public function order() { return $this->belongsTo(Order::class); }
    public function book() { return $this->belongsTo(Book::class); }
}