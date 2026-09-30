<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Book extends Model {
    protected $fillable = ['category_id', 'barcode', 'name', 'publish_date', 'stock', 'cost_price', 'sell_price', 'profit', 'description', 'cover_image'];

    public function category() { return $this->belongsTo(Category::class); }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($book) {
            if (empty($book->barcode)) {
                $book->barcode = self::generateBarcode();
            }
        });
    }

    public static function generateBarcode()
    {
        do {
            $barcode = '978' . str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }
}