<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');
        
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_books' => $totalBooks,
                'total_users' => $totalUsers,
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'recent_orders' => $recentOrders,
            ]
        ]);
    }
}
