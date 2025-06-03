<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Transaction
// Model untuk data transaksi penjualan (faktur/nota).
//
// Fitur utama:
// - Menyimpan data transaksi
// - Relasi ke item transaksi dan user
// - Accessor untuk format total dan tanggal

class Transaction extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'user_id',
        'customer',
        'payment_type',
        'total',
        'discount',
        'paid',
        'change'
    ];

    // Casting tipe data
    protected $casts = [
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid' => 'decimal:2',
        'change' => 'decimal:2'
    ];

    // Relasi ke item transaksi
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk format total
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    // Accessor untuk format tanggal
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }
} 