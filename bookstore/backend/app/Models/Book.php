<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Book extends Model {
    protected $fillable = ['category_id', 'name', 'publish_date', 'stock', 'cost_price', 'sell_price', 'profit', 'description', 'cover_image'];
    public function category() { return $this->belongsTo(Category::class); }
}