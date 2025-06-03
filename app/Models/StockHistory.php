<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// StockHistory
// Model untuk riwayat perubahan stok produk.
//
// Fitur utama:
// - Menyimpan data riwayat stok (masuk/keluar)
// - Relasi ke produk
// - Accessor untuk label dan warna tipe stok

class StockHistory extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'description'
    ];

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor untuk label tipe stok
    public function getTypeLabelAttribute()
    {
        return $this->type === 'in' ? 'Masuk' : 'Keluar';
    }

    // Accessor untuk warna badge tipe stok
    public function getTypeColorAttribute()
    {
        return $this->type === 'in' ? 'success' : 'danger';
    }
} 