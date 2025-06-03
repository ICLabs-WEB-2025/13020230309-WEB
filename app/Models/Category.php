<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Category
// Model untuk kategori produk di toko.
//
// Fitur utama:
// - Menyimpan data kategori
// - Relasi ke produk

class Category extends Model
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