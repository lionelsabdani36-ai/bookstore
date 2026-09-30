<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request) {
        $query = Order::where('status', 'paid');
        if ($request->has('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->has('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        $data = $query->with('details.book')->get();
        // Since dompdf could not be installed, we return JSON. In a real scenario we'd return a PDF stream.
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}