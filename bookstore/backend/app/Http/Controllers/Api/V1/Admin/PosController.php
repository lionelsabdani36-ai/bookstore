<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function scanBarcode(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);

        $book = Book::with('category')->where('barcode', $request->barcode)->first();

        if (!$book) {
            return response()->json([
                'status' => 'error',
                'message' => 'Book not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $book
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'cash_received' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            
            // Calculate total and prepare details
            $orderDetails = [];
            foreach ($validated['items'] as $item) {
                $subtotal = $item['qty'] * $item['unit_price'];
                $totalAmount += $subtotal;
                $orderDetails[] = [
                    'book_id' => $item['book_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ];
            }

            if ($validated['cash_received'] < $totalAmount) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cash received is less than total amount'
                ], 422);
            }

            $order = Order::create([
                'order_code' => 'POS-' . strtoupper(Str::random(6)),
                'user_id' => null,
                'status' => 'paid',
                'total_amount' => $totalAmount,
                'cash_received' => $validated['cash_received'],
                'change_amount' => $validated['cash_received'] - $totalAmount,
            ]);

            foreach ($orderDetails as $detail) {
                $order->details()->create($detail);
                
                // Decrement stock
                Book::where('id', $detail['book_id'])->decrement('stock', $detail['qty']);
            }

            return response()->json([
                'status' => 'success',
                'data' => $order->load('details.book')
            ], 201);
        });
    }
}
