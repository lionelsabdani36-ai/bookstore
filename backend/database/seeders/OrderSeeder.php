<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $order = Order::create([
            'order_code' => 'ORD-TEST01',
            'user_id' => 2, // customer
            'status' => 'pending',
            'total_amount' => 10.00
        ]);
        OrderDetail::create([
            'order_id' => $order->id,
            'book_id' => 1,
            'qty' => 1,
            'unit_price' => 10.00,
            'subtotal' => 10.00
        ]);
    }
}