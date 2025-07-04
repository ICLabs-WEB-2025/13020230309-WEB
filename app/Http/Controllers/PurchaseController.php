<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// PurchaseController
// Controller untuk mengelola pembelian barang dari supplier.
//
// Fitur utama:
// - Melihat daftar pembelian
// - Membuat, mengedit, dan menghapus pembelian
// - Menambah/mengurangi stok produk sesuai pembelian
// - Menampilkan detail pembelian

class PurchaseController extends Controller
{
    // Menampilkan daftar pembelian
    public function index()
    {
        $purchases = Purchase::with('user')->latest()->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    // Menampilkan form tambah pembelian baru
    public function create()
    {
        $products = Product::whereNotNull('category_id')
            ->select('id', 'code', 'name', 'price')
            ->get();
        return view('purchases.create', compact('products'));
    }

    // Menyimpan data pembelian baru
    // - Validasi input
    // - Hitung total
    // - Simpan pembelian dan item
    // - Update stok produk
    public function store(Request $request)
    {
        $request->validate([
            'supplier' => 'required',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total = collect($request->items)->sum(function($item) { 
                return $item['qty'] * $item['price']; 
            });

            $purchase = Purchase::create([
                'supplier' => $request->supplier,
                'tanggal' => $request->tanggal,
                'total' => $total,
                'keterangan' => $request->keterangan,
                'user_id' => Auth::id(),
            ]);

            // Simpan item pembelian dan update stok
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                ]);

                // Update stok produk
                $product->increment('stock', $item['qty']);
            }

            DB::commit();
            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', 'Pembelian berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan detail pembelian
    public function show($id)
    {
        $purchase = Purchase::with(['items.product', 'user'])->findOrFail($id);
        return view('purchases.show', compact('purchase'));
    }

    // Menampilkan form edit pembelian
    public function edit($id)
    {
        $purchase = Purchase::with(['items.product'])->findOrFail($id);
        $products = Product::whereNotNull('category_id')
            ->select('id', 'code', 'name', 'price')
            ->get();
        return view('purchases.edit', compact('purchase', 'products'));
    }

    // Mengupdate data pembelian
    // - Validasi input
    // - Kembalikan stok lama
    // - Hapus item lama
    // - Simpan item baru dan update stok
    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier' => 'required',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($id);
            
            // Kembalikan stok produk lama
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                $product->decrement('stock', $item->qty);
            }

            // Hapus item lama
            $purchase->items()->delete();

            // Hitung total baru
            $total = collect($request->items)->sum(function($item) { 
                return $item['qty'] * $item['price']; 
            });

            // Update data pembelian
            $purchase->update([
                'supplier' => $request->supplier,
                'tanggal' => $request->tanggal,
                'total' => $total,
                'keterangan' => $request->keterangan,
            ]);

            // Tambah item baru dan update stok
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                ]);

                // Update stok produk
                $product->increment('stock', $item['qty']);
            }

            DB::commit();
            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', 'Pembelian berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menghapus data pembelian
    // - Kembalikan stok produk
    // - Hapus item dan pembelian
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($id);
            
            // Kembalikan stok produk
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                $product->decrement('stock', $item->qty);
            }

            // Hapus item dan pembelian
            $purchase->items()->delete();
            $purchase->delete();

            DB::commit();
            return redirect()->route('purchases.index')
                ->with('success', 'Pembelian berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
    