<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Customer
// Model untuk data pelanggan toko.
//
// Fitur utama:
// - Menyimpan data pelanggan
// - Relasi ke transaksi

class Customer extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    // Relasi ke transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
} 