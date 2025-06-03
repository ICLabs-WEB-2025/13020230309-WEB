<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

// KasirController
// Controller utama untuk fitur kasir: pencarian produk, transaksi, dan menampilkan invoice.
//
// Fitur utama:
// - Menampilkan halaman kasir
// - Mencari produk
// - Menyimpan transaksi (beserta pengurangan stok dan detail item)
// - Menampilkan invoice transaksi

class KasirController extends Controller
{
    // Menampilkan halaman utama kasir
    public function index()
    {
        return view('kasir.index');
    }

    // Mencari produk berdasarkan kode atau nama
    // Request: q (string) - kata kunci pencarian
    // Response: JSON daftar produk
    public function search(Request $request)
    {
        $q = $request->input('q');
        $products = Product::where('code', 'like', "%$q%")
            ->orWhere('name', 'like', "%$q%")
            ->get();
        return response()->json($products);
    }

    // Menyimpan transaksi baru
    // - Validasi input transaksi dan item
    // - Hitung total, diskon, dan kembalian
    // - Simpan transaksi dan item ke database
    // - Kurangi stok produk
    // - Return: JSON status dan ID invoice
    public function store(Request $request) {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer' => 'required|string',
            'payment_type' => 'required|in:cash,transfer',
            'discount' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0',
        ]);

        // Mulai transaksi database untuk menjaga konsistensi data
        DB::beginTransaction();
        try {
            $total = 0;
            // Hitung total dan cek stok setiap item
            foreach ($request->items as $item) {
                $product = Product::with('unit')->findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception('Stok tidak cukup untuk ' . $product->name);
                }
                $total += $product->price * $item['quantity'];
            }
            $subTotal = $total - $request->discount;
            $change = $request->paid - $subTotal;

            // Simpan transaksi utama
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'customer' => $request->customer,
                'payment_type' => $request->payment_type,
                'total' => $total,
                'discount' => $request->discount,
                'paid' => $request->paid,
                'change' => $change,
            ]);

            // Simpan detail item transaksi dan update stok
            foreach ($request->items as $item) {
                $product = Product::with('unit')->findOrFail($item['product_id']);
                $product->decrement('stock', $item['quantity']);
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'unit' => $product->unit ? $product->unit->name : '-',
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity'],
                ]);
            }
            // Commit transaksi database jika semua proses sukses
            DB::commit();
            return response()->json(['success' => true, 'invoice_id' => $transaction->id]);
        } catch (\Exception $e) {
            // Rollback jika ada error
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // Menampilkan halaman invoice berdasarkan ID transaksi
    // Parameter: $id (int) - ID transaksi
    // Return: View invoice dengan data transaksi dan item
    public function invoice($id) {
        $transaction = Transaction::with('items')->findOrFail($id);
        return view('kasir.invoice', compact('transaction'));
    }
}
