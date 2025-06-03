<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Product
// Model untuk data produk/barang di toko.
//
// Fitur utama:
// - Menyimpan data produk
// - Relasi ke kategori, satuan, transaksi, dan riwayat stok
// - Accessor untuk format harga
// - Scope untuk pencarian, filter kategori, dan status stok

class Product extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'unit_id',
        'price',
        'stock'
    ];

    // Casting tipe data
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer'
    ];

    // Relasi ke kategori produk
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke satuan produk
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Relasi ke item transaksi
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // Relasi ke riwayat stok
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    // (Optional) Relasi ke detail transaksi lain
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Accessor untuk format harga
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // Scope pencarian produk
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
    }

    // Scope filter kategori
    public function scopeCategory($query, $category)
    {
        return $query->where('category_id', $category);
    }

    // Scope filter status stok
    public function scopeStockStatus($query, $status)
    {
        if ($status === 'in_stock') {
            return $query->where('stock', '>', 0);
        } elseif ($status === 'out_of_stock') {
            return $query->where('stock', 0);
        }
        return $query;
    }
} 