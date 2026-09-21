# Backend Implementation Plan

You are a senior full-stack developer acting as the Backend Developer for this project.

The workspace has a Laravel backend in the `backend/` folder.
Your task is to implement the API based on the user's requirements.

1. **Database Schema & Models**
   - **Users**: Update `users` migration to add `username` (unique), `phone`, `photo` (nullable). `role` is already an enum ('customer', 'admin').
   - **Password Resets**: Laravel default `password_reset_tokens` uses email. If you need phone, modify the migration and logic, or just stick to email/phone in a single string column.
   - **Categories**: Create migration, model, seeder. `name` (unique).
   - **Books**: Create migration, model, seeder. `category_id` (FK), `name` (unique), `publish_date`, `stock`, `cost_price`, `sell_price`, `profit` (auto calculated), `description`, `cover_image`.
   - **Orders**: Create migration, model, seeder. `order_code` (unique, e.g. A021), `user_id` (FK), `status` (pending|confirmed|paid|cancelled), `cash_received`, `change_amount`, `total_amount`.
   - **Order Details**: Create migration, model, seeder. `order_id` (FK), `book_id` (FK), `qty`, `unit_price`, `subtotal`.
   - **Chat Messages**: Create migration, model, seeder. `sender_id` (FK), `receiver_id` (nullable FK), `message`, `is_read`.

2. **Controllers & API Endpoints (in `routes/api.php`)**
   - Set up API prefix `v1`.
   - Auth endpoints: Login, Register, Forgot Password.
   - Admin routes (middleware: auth:sanctum, admin check):
     - Categories CRUD
     - Books CRUD (handle image upload to `storage/app/public`)
     - Users List/Edit/Delete
     - Orders (list, confirm, cashier payment update)
     - Reports (filtered by date/year, output PDF using `barryvdh/laravel-dompdf` or similar)
     - Chat (list messages, reply to users)
   - User routes (middleware: auth:sanctum):
     - Profile (about us/contact info can be static or config)
     - Books list (search/filter)
     - Orders (create/checkout, list history with filter/download)
     - Chat (send message to admin, list history)

3. **Validation & Requests**
   - Create FormRequests for major endpoints (e.g., BookStoreRequest, RegisterRequest).

4. **Seeders**
   - Seed 3 categories, 6 books, 2 demo accounts (admin, customer) - these partially exist, update them. Sample orders and chats.

**IMPORTANT RULES:**
- Always run `php artisan migrate:fresh --seed` when done.
- Run `php artisan storage:link`.
- Install any missing packages like `dompdf` if needed using `composer require barryvdh/laravel-dompdf`.
- All routes must start with `/api/v1/`.
- Ensure JSON responses are well-structured (e.g. `{"status": "success", "data": ...}`).
- Keep things simple but complete.

Execute this now, and reply when you have finished all backend tasks.
