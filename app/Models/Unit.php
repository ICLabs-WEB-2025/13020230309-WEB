<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Unit
// Model untuk satuan produk (misal: pcs, kg, liter).
//
// Fitur utama:
// - Menyimpan data satuan
// - Relasi ke produk

class Unit extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'name',
        'description',
    ];

    // Relasi ke produk
    public function products()
    {
        return $this->hasMany(Product::class);
    }
} 