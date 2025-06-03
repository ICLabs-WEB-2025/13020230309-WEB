<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Expense
// Model untuk data pengeluaran toko.
//
// Fitur utama:
// - Menyimpan data pengeluaran
// - Accessor untuk format nominal dan tanggal

class Expense extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'date',
        'description',
        'amount',
        'category',
        'notes'
    ];

    // Casting tipe data
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    // Accessor untuk format nominal
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Accessor untuk format tanggal
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }
} 