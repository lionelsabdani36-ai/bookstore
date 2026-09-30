<?php
namespace App\Http\Controllers\Api\V1\User;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Book;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request) {
        $orders = Order::where('user_id', $request->user()->id)->with('details.book')->get();
        return response()->json(['status' => 'success', 'data' => $orders]);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);
        
        $order = Order::create([
            'order_code' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'total_amount' => 0,
        ]);
        
        $total = 0;
        foreach ($validated['items'] as $item) {
            $book = Book::find($item['book_id']);
            $subtotal = $book->sell_price * $item['qty'];
            OrderDetail::create([
                'order_id' => $order->id,
                'book_id' => $book->id,
                'qty' => $item['qty'],
                'unit_price' => $book->sell_price,
                'subtotal' => $subtotal,
            ]);
            $total += $subtotal;
            $book->decrement('stock', $item['qty']);
        }
        
        $order->update(['total_amount' => $total]);
        return response()->json(['status' => 'success', 'data' => $order->load('details')]);
    }
}