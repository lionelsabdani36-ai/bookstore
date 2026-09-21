<?php
$files = [
    'app/Models/User.php' => <<<'PHP'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = ['name', 'username', 'email', 'phone', 'photo', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }
}
PHP,
    'app/Models/Category.php' => <<<'PHP'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model {
    protected $fillable = ['name'];
    public function books() { return $this->hasMany(Book::class); }
}
PHP,
    'app/Models/Book.php' => <<<'PHP'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Book extends Model {
    protected $fillable = ['category_id', 'name', 'publish_date', 'stock', 'cost_price', 'sell_price', 'profit', 'description', 'cover_image'];
    public function category() { return $this->belongsTo(Category::class); }
}
PHP,
    'app/Models/Order.php' => <<<'PHP'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
    protected $fillable = ['order_code', 'user_id', 'status', 'total_amount', 'cash_received', 'change_amount'];
    public function user() { return $this->belongsTo(User::class); }
    public function details() { return $this->hasMany(OrderDetail::class); }
}
PHP,
    'app/Models/OrderDetail.php' => <<<'PHP'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderDetail extends Model {
    protected $fillable = ['order_id', 'book_id', 'qty', 'unit_price', 'subtotal'];
    public function order() { return $this->belongsTo(Order::class); }
    public function book() { return $this->belongsTo(Book::class); }
}
PHP,
    'app/Models/ChatMessage.php' => <<<'PHP'
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ChatMessage extends Model {
    protected $fillable = ['sender_id', 'receiver_id', 'message', 'is_read'];
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver() { return $this->belongsTo(User::class, 'receiver_id'); }
}
PHP,
    'routes/api.php' => <<<'PHP'
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\User;

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        // User Routes
        Route::get('profile', [User\ProfileController::class, 'index']);
        Route::get('books', [User\BookController::class, 'index']);
        Route::get('orders', [User\OrderController::class, 'index']);
        Route::post('orders', [User\OrderController::class, 'store']);
        Route::get('chat', [User\ChatController::class, 'index']);
        Route::post('chat', [User\ChatController::class, 'store']);

        // Admin Routes
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::apiResource('categories', Admin\CategoryController::class);
            Route::apiResource('books', Admin\BookController::class);
            Route::apiResource('users', Admin\UserController::class);
            Route::apiResource('orders', Admin\OrderController::class);
            Route::get('reports', [Admin\ReportController::class, 'index']);
            Route::get('chat', [Admin\ChatController::class, 'index']);
            Route::post('chat', [Admin\ChatController::class, 'store']);
        });
    });
});
PHP,
    'app/Http/Middleware/AdminMiddleware.php' => <<<'PHP'
<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request);
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
PHP,
    'bootstrap/app.php' => <<<'PHP'
<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
PHP,
];

foreach ($files as $path => $content) {
    file_put_contents(__DIR__ . '/' . $path, $content);
}
echo "Files updated!\n";
