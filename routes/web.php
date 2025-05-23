<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PurchaseController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CRUD routes tanpa middleware
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Master Data
Route::resource('categories', CategoryController::class);
Route::resource('units', UnitController::class);
Route::resource('customers', CustomerController::class);
Route::resource('products', ProductController::class);

// Transactions (Penjualan)
Route::resource('transactions', TransactionController::class);
Route::post('transactions/add-item', [TransactionController::class, 'addItem'])->name('transactions.add-item');
Route::delete('transactions/remove-item/{id}', [TransactionController::class, 'removeItem'])->name('transactions.remove-item');

// Expenses
Route::resource('expenses', ExpenseController::class);

// Reports
Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
Route::get('reports/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');

// Kasir
Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
Route::get('/kasir/search', [KasirController::class, 'search'])->name('kasir.search');
Route::post('/kasir/store', [KasirController::class, 'store'])->name('kasir.store');
Route::get('/kasir/invoice/{id}', [KasirController::class, 'invoice'])->name('kasir.invoice');

// Purchases (Pembelian)
Route::resource('purchases', PurchaseController::class);
