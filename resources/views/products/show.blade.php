@extends('layouts.app')

@section('title', 'Detail Produk - Kasir Toko')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Detail Produk</h1>
            <div>
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th style="width: 200px;">Kode Produk</th>
                        <td>{{ $product->code }}</td>
                    </tr>
                    <tr>
                        <th>Nama Produk</th>
                        <td>{{ $product->name }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $product->category ? $product->category->name : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Stok</th>
                        <td>
                            <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $product->description ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat Pada</th>
                        <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diperbarui</th>
                        <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Stok</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($product->stockHistories as $history)
                                    <tr>
                                        <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge {{ $history->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $history->type === 'in' ? 'Masuk' : 'Keluar' }}
                                            </span>
                                        </td>
                                        <td>{{ $history->quantity }}</td>
                                        <td>{{ $history->description }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada riwayat stok</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 