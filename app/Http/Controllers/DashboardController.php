<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total penjualan hari ini
        $todaySales = Transaction::whereDate('created_at', Carbon::today())
            ->sum('total');

        // Total pengeluaran hari ini
        $todayExpenses = Expense::whereDate('date', Carbon::today())
            ->sum('amount');

        // Laba/rugi hari ini
        $todayProfit = $todaySales - $todayExpenses;

        // Produk terlaris (top 5)
        $topProducts = Product::select([
                'products.id',
                'products.code',
                'products.name',
                'products.price',
                DB::raw('SUM(transaction_items.quantity) as total_sold')
            ])
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->whereDate('transactions.created_at', Carbon::today())
            ->groupBy('products.id', 'products.code', 'products.name', 'products.price')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Total barang terjual bulan ini
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $totalItemsSoldMonth = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$monthStart, $monthEnd])
            ->sum('transaction_items.quantity');

        // Jumlah barang (produk)
        $totalProducts = Product::count();

        // Total invoice penjualan bulan ini
        $totalInvoicesMonth = Transaction::whereBetween('created_at', [$monthStart, $monthEnd])->count();

        // Data stok terkecil (4 terendah)
        $lowestStockProducts = Product::orderBy('stock', 'asc')->limit(4)->get();

        return view('dashboard', compact(
            'todaySales',
            'todayExpenses',
            'todayProfit',
            'topProducts',
            'totalItemsSoldMonth',
            'totalProducts',
            'totalInvoicesMonth',
            'lowestStockProducts'
        ));
    }
} 