<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir.index');
    }

    public function search(Request $request)
    {
        $q = $request->input('q');
        $products = Product::where('code', 'like', "%$q%")
            ->orWhere('name', 'like', "%$q%")
            ->get();
        return response()->json($products);
    }

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

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::with('unit')->findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception('Stok tidak cukup untuk ' . $product->name);
                }
                $total += $product->price * $item['quantity'];
            }
            $subTotal = $total - $request->discount;
            $change = $request->paid - $subTotal;

            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'customer' => $request->customer,
                'payment_type' => $request->payment_type,
                'total' => $total,
                'discount' => $request->discount,
                'paid' => $request->paid,
                'change' => $change,
            ]);

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
            DB::commit();
            return response()->json(['success' => true, 'invoice_id' => $transaction->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function invoice($id) {
        $transaction = Transaction::with('items')->findOrFail($id);
        return view('kasir.invoice', compact('transaction'));
    }
}
