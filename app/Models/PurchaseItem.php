<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// PurchaseItem
// Model untuk item/detail pembelian (satu baris produk dalam satu pembelian).
//
// Fitur utama:
// - Menyimpan data item pembelian
// - Relasi ke pembelian dan produk

class PurchaseItem extends Model
{
    // Kolom yang bisa diisi massal
    protected $fillable = [
        'purchase_id', 'product_id', 'qty', 'price', 'subtotal'
    ];

    // Relasi ke pembelian induk
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
