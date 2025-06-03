<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

// UnitController
// Controller untuk mengelola satuan produk (unit) di toko.
//
// Fitur utama:
// - Melihat daftar satuan
// - Menambah, mengedit, dan menghapus satuan

class UnitController extends Controller
{
    // Menampilkan daftar satuan
    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    // Menampilkan form tambah satuan
    public function create()
    {
        return view('units.create');
    }

    // Menyimpan satuan baru
    // - Validasi input
    // - Simpan satuan ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:units,name',
            'description' => 'nullable'
        ]);
        Unit::create($request->all());
        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan!');
    }

    // Menampilkan form edit satuan
    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    // Mengupdate data satuan
    // - Validasi input
    // - Update satuan di database
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|unique:units,name,' . $unit->id,
            'description' => 'nullable'
        ]);
        $unit->update($request->all());
        return redirect()->route('units.index')->with('success', 'Satuan berhasil diupdate!');
    }

    // Menghapus satuan
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus!');
    }
}
