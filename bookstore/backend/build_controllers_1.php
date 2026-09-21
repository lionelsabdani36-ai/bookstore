<?php
$files = [
    'app/Http/Controllers/Api/V1/AuthController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken
            ]
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials']]);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken
            ]
        ]);
    }
}
PHP,
    'app/Http/Controllers/Api/V1/Admin/CategoryController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() { return response()->json(['status' => 'success', 'data' => Category::all()]); }
    public function store(Request $request) {
        $validated = $request->validate(['name' => 'required|string|unique:categories']);
        return response()->json(['status' => 'success', 'data' => Category::create($validated)]);
    }
    public function show(Category $category) { return response()->json(['status' => 'success', 'data' => $category]); }
    public function update(Request $request, Category $category) {
        $validated = $request->validate(['name' => 'required|string|unique:categories,name,' . $category->id]);
        $category->update($validated);
        return response()->json(['status' => 'success', 'data' => $category]);
    }
    public function destroy(Category $category) {
        $category->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}
PHP,
    'app/Http/Controllers/Api/V1/Admin/BookController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index() { return response()->json(['status' => 'success', 'data' => Book::with('category')->get()]); }
    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|unique:books',
            'publish_date' => 'nullable|date',
            'stock' => 'required|integer',
            'cost_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image',
        ]);
        $validated['profit'] = $validated['sell_price'] - $validated['cost_price'];
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }
        return response()->json(['status' => 'success', 'data' => Book::create($validated)]);
    }
    public function show(Book $book) { return response()->json(['status' => 'success', 'data' => $book->load('category')]); }
    public function update(Request $request, Book $book) {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|unique:books,name,' . $book->id,
            'publish_date' => 'nullable|date',
            'stock' => 'required|integer',
            'cost_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image',
        ]);
        $validated['profit'] = $validated['sell_price'] - $validated['cost_price'];
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }
        $book->update($validated);
        return response()->json(['status' => 'success', 'data' => $book]);
    }
    public function destroy(Book $book) {
        $book->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}
PHP,
    'app/Http/Controllers/Api/V1/Admin/UserController.php' => <<<'PHP'
<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() { return response()->json(['status' => 'success', 'data' => User::all()]); }
    public function show(User $user) { return response()->json(['status' => 'success', 'data' => $user]); }
    public function destroy(User $user) {
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}
PHP,
    'app/Http/Controllers/Api/V1/Admin/OrderController.php' => <<<'PHP'
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
PHP,
];
foreach ($files as $path => $content) {
    file_put_contents(__DIR__ . '/' . $path, $content);
}
echo "Controllers updated!\n";
