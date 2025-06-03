<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// TransactionItem
// Model untuk item/detail transaksi penjualan (satu baris produk dalam satu transaksi).
//
// Fitur utama:
// - Menyimpan data item transaksi
// - Relasi ke transaksi dan produk
// - Accessor untuk format harga dan subtotal

class TransactionItem extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'transaction_id',
        'product_id',
        'name',
        'unit',
        'price',
        'quantity',
        'subtotal'
    ];

    // Casting tipe data
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2'
    ];

    // Relasi ke transaksi induk
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor untuk format harga
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // Accessor untuk format subtotal
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
} 