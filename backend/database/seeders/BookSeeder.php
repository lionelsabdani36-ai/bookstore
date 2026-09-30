<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Book;
class BookSeeder extends Seeder
{
    public function run(): void
    {
        for($i=1; $i<=6; $i++) {
            Book::create([
                'category_id' => rand(1, 3),
                'name' => "Sample Book $i",
                'stock' => 10,
                'cost_price' => 5.00,
                'sell_price' => 10.00,
                'profit' => 5.00,
                'description' => "Description for book $i"
            ]);
        }
    }
}