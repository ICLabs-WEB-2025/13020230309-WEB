<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

// ExpenseController
// Controller untuk mengelola data pengeluaran toko.
//
// Fitur utama:
// - Melihat daftar pengeluaran
// - Menambah, mengedit, dan menghapus pengeluaran

class ExpenseController extends Controller
{
    // Menampilkan daftar pengeluaran
    public function index()
    {
        $expenses = Expense::with('user')
            ->latest()
            ->paginate(10);
        return view('expenses.index', compact('expenses'));
    }

    // Menampilkan form tambah pengeluaran
    public function create()
    {
        return view('expenses.create');
    }

    // Menyimpan pengeluaran baru
    // - Validasi input
    // - Simpan pengeluaran ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        $validated['user_id'] = auth()->id();

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    // Menampilkan form edit pengeluaran
    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    // Mengupdate data pengeluaran
    // - Validasi input
    // - Update pengeluaran di database
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    // Menghapus pengeluaran
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }
} 