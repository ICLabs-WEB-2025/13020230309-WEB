<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['kategori', 'satuan'])->latest()->paginate(10);
        return view('barangs.index', compact('barangs'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        $satuans = Satuan::all();
        return view('barangs.create', compact('kategoris', 'satuans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|unique:barangs',
            'nama' => 'required',
            'deskripsi' => 'nullable',
            'kategori_id' => 'required|exists:categories,id',
            'sn_type' => 'required|in:SN,non-SN',
            'stock' => 'required|integer|min:0',
            'satuan_id' => 'required|exists:units,id',
            'harga_umum' => 'required|numeric|min:0',
        ]);
        Barang::create($request->all());
        return redirect()->route('barangs.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function show(Barang $barang)
    {
        return view('barangs.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $kategoris = Kategori::all();
        $satuans = Satuan::all();
        return view('barangs.edit', compact('barang', 'kategoris', 'satuans'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'barcode' => 'required|unique:barangs,barcode,' . $barang->id,
            'nama' => 'required',
            'deskripsi' => 'nullable',
            'kategori_id' => 'required|exists:categories,id',
            'sn_type' => 'required|in:SN,non-SN',
            'stock' => 'required|integer|min:0',
            'satuan_id' => 'required|exists:units,id',
            'harga_umum' => 'required|numeric|min:0',
        ]);
        $barang->update($request->all());
        return redirect()->route('barangs.index')->with('success', 'Barang berhasil diupdate!');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('barangs.index')->with('success', 'Barang berhasil dihapus!');
    }
} 