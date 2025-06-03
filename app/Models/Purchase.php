<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Purchase
// Model untuk data pembelian barang dari supplier.
//
// Fitur utama:
// - Menyimpan data pembelian
// - Relasi ke item pembelian dan user

class Purchase extends Model
{
    // Kolom yang bisa diisi massal
    protected $fillable = [
        'supplier', 'tanggal', 'total', 'keterangan', 'user_id'
    ];

    // Relasi ke item pembelian
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
