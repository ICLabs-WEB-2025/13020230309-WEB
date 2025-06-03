<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// TransactionController
// Controller untuk mengelola transaksi penjualan secara umum (bukan kasir harian).
//
// Fitur utama:
// - Melihat daftar transaksi
// - Membuat, mengedit, dan menghapus transaksi
// - Mengelola item transaksi dan stok produk

class TransactionController extends Controller
{
    // Menampilkan daftar transaksi
    public function index()
    {
        $transactions = Transaction::with('items')
            ->latest()
            ->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    // Menampilkan form tambah transaksi
    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('transactions.create', compact('products'));
    }

    // Menyimpan transaksi baru
    // - Validasi input
    // - Simpan transaksi dan item
    // - Kurangi stok produk
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                'total' => 0,
                'user_id' => auth()->id(),
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $transaction->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);
                $total += $product->price * $item['quantity'];
            }

            $transaction->update(['total' => $total]);
            DB::commit();

            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaction completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Menampilkan detail transaksi
    public function show(Transaction $transaction)
    {
        $transaction->load('items.product');
        return view('transactions.show', compact('transaction'));
    }

    // Menampilkan form edit transaksi
    public function edit(Transaction $transaction)
    {
        $transaction->load('items.product');
        $products = Product::where('stock', '>', 0)->orWhereIn('id', $transaction->items->pluck('product_id'))->get();
        return view('transactions.edit', compact('transaction', 'products'));
    }

    // Mengupdate data transaksi
    // - Validasi input
    // - Kembalikan stok lama
    // - Hapus item lama
    // - Simpan item baru dan update stok
    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Kembalikan stok produk lama
            foreach ($transaction->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
            $transaction->items()->delete();

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
                $transaction->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);
                $product->decrement('stock', $item['quantity']);
                $total += $product->price * $item['quantity'];
            }
            $transaction->update(['total' => $total]);
            DB::commit();
            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // Menghapus transaksi
    // - Kembalikan stok produk
    // - Hapus item dan transaksi
    public function destroy(Transaction $transaction)
    {
        try {
            DB::beginTransaction();
            // Kembalikan stok produk
            foreach ($transaction->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
            $transaction->items()->delete();
            $transaction->delete();
            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
} 