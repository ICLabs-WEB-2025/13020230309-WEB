<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalTransactions = Transaction::count();
        $totalRevenue = Transaction::sum('total_amount');
        
        $recentTransactions = Transaction::with('items')
            ->latest()
            ->take(5)
            ->get();
            
        $lowStockProducts = Product::where('stock', '<', 10)
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalTransactions',
            'totalRevenue',
            'recentTransactions',
            'lowStockProducts'
        ));
    }
} 