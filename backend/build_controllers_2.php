<?php
$files = [
    'app/Http/Controllers/Api/V1/Admin/ReportController.php' => <<<'PHP'
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
PHP,
    'app/Http/Controllers/Api/V1/Admin/ChatController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request) {
        $messages = ChatMessage::with(['sender', 'receiver'])->get();
        return response()->json(['status' => 'success', 'data' => $messages]);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
        $validated['sender_id'] = $request->user()->id;
        $chat = ChatMessage::create($validated);
        return response()->json(['status' => 'success', 'data' => $chat]);
    }
}
PHP,
    'app/Http/Controllers/Api/V1/User/ProfileController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Api\V1\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $request->user(),
                'about_us' => 'Bookstore API V1',
                'contact' => 'support@bookstore.test'
            ]
        ]);
    }
}
PHP,
    'app/Http/Controllers/Api/V1/User/BookController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Api\V1\User;
use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request) {
        $query = Book::with('category');
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        return response()->json(['status' => 'success', 'data' => $query->get()]);
    }
}
PHP,
    'app/Http/Controllers/Api/V1/User/OrderController.php' => <<<'PHP'
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
PHP,
    'app/Http/Controllers/Api/V1/User/ChatController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Api\V1\User;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request) {
        $messages = ChatMessage::where('sender_id', $request->user()->id)
            ->orWhere('receiver_id', $request->user()->id)
            ->with(['sender', 'receiver'])
            ->get();
        return response()->json(['status' => 'success', 'data' => $messages]);
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
        $admin = User::where('role', 'admin')->first();
        $chat = ChatMessage::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $admin ? $admin->id : null,
            'message' => $validated['message']
        ]);
        return response()->json(['status' => 'success', 'data' => $chat]);
    }
}
PHP,
];
foreach ($files as $path => $content) {
    file_put_contents(__DIR__ . '/' . $path, $content);
}
echo "Controllers 2 updated!\n";
