<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(30));
        $endDate = $request->input('end_date', Carbon::today());

        $sales = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(total) as total_sales')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalSales = $sales->sum('total_sales');
        $totalTransactions = $sales->sum('total_transactions');

        return view('reports.sales', compact('sales', 'totalSales', 'totalTransactions', 'startDate', 'endDate'));
    }

    public function expenses(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(30));
        $endDate = $request->input('end_date', Carbon::today());

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(expense_date) as date'),
                DB::raw('COUNT(*) as total_expenses'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalExpenses = $expenses->sum('total_amount');
        $totalExpenseCount = $expenses->sum('total_expenses');

        return view('reports.expenses', compact('expenses', 'totalExpenses', 'totalExpenseCount', 'startDate', 'endDate'));
    }

    public function financial(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(30));
        $endDate = $request->input('end_date', Carbon::today());

        // Get daily sales
        $sales = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total_sales')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get daily expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(expense_date) as date'),
                DB::raw('SUM(amount) as total_expenses')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate totals
        $totalSales = $sales->sum('total_sales');
        $totalExpenses = $expenses->sum('total_expenses');
        $profit = $totalSales - $totalExpenses;

        // Get top selling products
        $topProducts = Product::select('products.*')
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->groupBy('products.id')
            ->orderByRaw('SUM(transaction_items.quantity) DESC')
            ->take(5)
            ->get();

        return view('reports.financial', compact(
            'sales',
            'expenses',
            'totalSales',
            'totalExpenses',
            'profit',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }

    public function inventory()
    {
        $products = Product::select(
                'products.*',
                DB::raw('(SELECT COUNT(*) FROM transaction_items WHERE product_id = products.id) as times_sold')
            )
            ->orderBy('stock')
            ->paginate(15);

        $lowStock = Product::where('stock', '<', 10)->count();
        $outOfStock = Product::where('stock', 0)->count();

        return view('reports.inventory', compact('products', 'lowStock', 'outOfStock'));
    }
} 