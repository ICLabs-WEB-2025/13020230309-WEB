{{--
    index.blade.php (Products)
    Halaman daftar produk/barang.
    Fitur:
    - Tabel daftar produk
    - Link ke tambah, edit, dan detail produk
--}}
@extends('layouts.app')

@section('title', 'Daftar Produk - Kasir Toko')

@section('content')
{{-- Card utama daftar produk --}}
<div class="card shadow-sm">
    <div class="card-body">
        {{-- Header dan tombol tambah produk --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Daftar Produk</h1>
            {{-- Tombol tambah produk --}}
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Produk
            </a>
        </div>

        {{-- Tabel daftar produk --}}
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop data produk --}}
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $product->code }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category ? $product->category->name : '-' }}</td>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            {{-- Tombol aksi edit & hapus produk --}}
                            <div class="btn-group">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada produk</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination jika ada banyak produk --}}
        @if($products->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 