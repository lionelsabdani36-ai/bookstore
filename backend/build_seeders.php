<?php
$files = [
    'database/seeders/UserSeeder.php' => <<<'PHP'
<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        User::create([
            'name' => 'Customer User',
            'username' => 'customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer'
        ]);
    }
}
PHP,
    'database/seeders/CategorySeeder.php' => <<<'PHP'
<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Category;
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            ['name' => 'Fiction'],
            ['name' => 'Non-Fiction'],
            ['name' => 'Science']
        ]);
    }
}
PHP,
    'database/seeders/BookSeeder.php' => <<<'PHP'
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
PHP,
    'database/seeders/OrderSeeder.php' => <<<'PHP'
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
PHP,
    'database/seeders/ChatMessageSeeder.php' => <<<'PHP'
<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\ChatMessage;
class ChatMessageSeeder extends Seeder
{
    public function run(): void
    {
        ChatMessage::create([
            'sender_id' => 2,
            'receiver_id' => 1,
            'message' => 'Hello admin, I need help.'
        ]);
    }
}
PHP,
    'database/seeders/DatabaseSeeder.php' => <<<'PHP'
<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            BookSeeder::class,
            OrderSeeder::class,
            ChatMessageSeeder::class,
        ]);
    }
}
PHP,
];
foreach ($files as $path => $content) {
    file_put_contents(__DIR__ . '/' . $path, $content);
}
echo "Seeders updated!\n";
