<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('kasir.index', compact('products'));
    }

    public function search(Request $request)
    {
        $q = $request->input('q');
        $products = Product::where('code', 'like', "%$q%")
            ->orWhere('name', 'like', "%$q%")
            ->get();
        return response()->json($products);
    }
}
