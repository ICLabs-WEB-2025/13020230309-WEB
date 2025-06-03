<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockHistory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Pencarian
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter kategori
        if ($request->has('category')) {
            $query->category($request->category);
        }

        // Filter stok
        if ($request->has('stock_status')) {
            $query->stockStatus($request->stock_status);
        }

        $products = $query->latest()->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products',
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create($validated);

            // Catat riwayat stok awal
            if ($validated['stock'] > 0) {
                StockHistory::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $validated['stock'],
                    'description' => 'Stok awal'
                ]);
            }

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load('stockHistories');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            // Hitung selisih stok
            $stockDiff = $validated['stock'] - $product->stock;

            // Update produk
            $product->update($validated);

            // Catat perubahan stok jika ada
            if ($stockDiff != 0) {
                StockHistory::create([
                    'product_id' => $product->id,
                    'type' => $stockDiff > 0 ? 'in' : 'out',
                    'quantity' => abs($stockDiff),
                    'description' => 'Update stok manual'
                ]);
            }

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Hapus riwayat stok
            $product->stockHistories()->delete();
            
            // Hapus produk
            $product->delete();

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->select('id', 'name', 'code')
            ->limit(10)
            ->get();
        
        return response()->json($products);
    }
} 