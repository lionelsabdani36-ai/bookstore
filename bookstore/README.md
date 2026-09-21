# Toko Buku (BookStore) Monorepo

Welcome to the BookStore application, a complete full-stack web application designed for a school practical assignment. This project is structured as a monorepo containing a Laravel REST API backend and a Nuxt 3 frontend.

## Tech Stack
- **Backend**: Laravel 11, PostgreSQL, Laravel Sanctum (Auth), barryvdh/laravel-dompdf (PDF Generation)
- **Frontend**: Nuxt 3 (Vue 3 Composition API), Pinia, Tailwind CSS

---

## Getting Started

### 1. Backend Setup (Laravel API)
Navigate to the `backend/` directory:
```bash
cd backend
```
Install dependencies (if not already done):
```bash
composer install
```
Configure your `.env` file for PostgreSQL database credentials (already configured for this environment).
Run migrations and seed the database to generate categories, books, and demo users:
```bash
php artisan migrate:fresh --seed
```
Link the storage directory for uploaded cover images:
```bash
php artisan storage:link
```
Start the Laravel development server:
```bash
php artisan serve
```
*The API will be available at `http://localhost:8000/api/v1/`.*

---

### 2. Frontend Setup (Nuxt 3)
Open a new terminal and navigate to the `frontend/` directory:
```bash
cd frontend
```
Install Node.js dependencies:
```bash
npm install
```
Start the Nuxt development server:
```bash
npm run dev
```
*The web application will be available at `http://localhost:3000/`.*

---

## Demo Credentials
The `DatabaseSeeder` has pre-populated the system with two demo accounts for testing out the different roles:

**Admin Account**
- **Email**: admin@example.com (or Username: admin)
- **Password**: password
- **Role**: Admin (Full access to Categories, Books, Orders, Reports, Users)

**User (Pembeli) Account**
- **Email**: customer@example.com (or Username: customer)
- **Password**: password
- **Role**: Customer (Access to Catalog, Cart, Order History)

---

## Features Implemented
- **Authentication**: Login, Register, Forgot Password with hashed passwords (bcrypt).
- **Admin Dashboard**: Manage Books (CRUD + image upload), Categories, Users, confirm Orders, generate Reports (PDF), and reply to Chats.
- **User Dashboard**: Browse Books, add to Cart, Checkout, view Order History, and Chat with Admin.
- **Responsive UI**: Built with Tailwind CSS, utilizing a soft-gradient sidebar and rounded cards per specifications.

Happy Coding!
