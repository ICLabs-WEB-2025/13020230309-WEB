<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

// CustomerController
// Controller untuk mengelola data pelanggan toko.
//
// Fitur utama:
// - Melihat daftar pelanggan
// - Menambah, mengedit, dan menghapus pelanggan

class CustomerController extends Controller
{
    // Menampilkan daftar pelanggan
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }

    // Menampilkan form tambah pelanggan
    public function create()
    {
        return view('customers.create');
    }

    // Menyimpan pelanggan baru
    // - Validasi input
    // - Simpan pelanggan ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers',
            'address' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    // Menampilkan form edit pelanggan
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    // Mengupdate data pelanggan
    // - Validasi input
    // - Update pelanggan di database
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    // Menghapus pelanggan
    // - Tidak bisa hapus jika masih ada transaksi
    public function destroy(Customer $customer)
    {
        if ($customer->transactions()->exists()) {
            return back()->with('error', 'Pelanggan tidak dapat dihapus karena masih memiliki transaksi.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
} 