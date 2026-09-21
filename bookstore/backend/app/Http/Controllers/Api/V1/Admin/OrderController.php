<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index() { return response()->json(['status' => 'success', 'data' => Order::with(['user', 'details.book'])->get()]); }
    public function show(Order $order) { return response()->json(['status' => 'success', 'data' => $order->load(['user', 'details.book'])]); }
    public function update(Request $request, Order $order) {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,paid,cancelled',
            'cash_received' => 'nullable|numeric',
        ]);
        if (isset($validated['cash_received']) && $validated['status'] === 'paid') {
            $validated['change_amount'] = $validated['cash_received'] - $order->total_amount;
        }
        $order->update($validated);
        return response()->json(['status' => 'success', 'data' => $order]);
    }
}