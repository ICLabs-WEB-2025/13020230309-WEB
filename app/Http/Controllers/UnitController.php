<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:units,name',
            'description' => 'nullable'
        ]);
        Unit::create($request->all());
        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan!');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|unique:units,name,' . $unit->id,
            'description' => 'nullable'
        ]);
        $unit->update($request->all());
        return redirect()->route('units.index')->with('success', 'Satuan berhasil diupdate!');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus!');
    }
}
