<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'description'
    ];

    // Relasi dengan produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor untuk tipe stok
    public function getTypeLabelAttribute()
    {
        return $this->type === 'in' ? 'Masuk' : 'Keluar';
    }

    // Accessor untuk warna badge
    public function getTypeColorAttribute()
    {
        return $this->type === 'in' ? 'success' : 'danger';
    }
} 