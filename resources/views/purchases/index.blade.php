{{--
    index.blade.php (Purchases)
    Halaman daftar pembelian barang dari supplier.
    Fitur:
    - Tabel daftar pembelian
    - Link ke tambah, edit, dan detail pembelian
--}}
@extends('layouts.app')
@section('title', 'Daftar Pembelian')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Pembelian Produk</h3>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Pembelian</a>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->tanggal }}</td>
                        <td>{{ $purchase->supplier }}</td>
                        <td>Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                        <td>{{ $purchase->user->name ?? '-' }}</td>
                        <td><a href="{{ route('purchases.show', $purchase) }}" class="btn btn-info btn-sm">Detail</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection 