<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->paginate(10);
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'description' => 'nullable|string',
        ]);

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'description' => 'nullable|string',
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->products()->exists()) {
            return back()->with('error', 'Satuan tidak dapat dihapus karena masih memiliki produk.');
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil dihapus.');
    }
} 