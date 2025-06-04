{{--
    show.blade.php (Purchases)
    Halaman detail pembelian barang dari supplier.
    Fitur:
    - Menampilkan detail supplier, tanggal, keterangan
    - Tabel produk yang dibeli
--}}
@extends('layouts.app')
@section('title', 'Detail Pembelian')
@section('content')
<div class="container">
    <h3 class="mb-4">Detail Pembelian</h3>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4"><b>Supplier:</b> {{ $purchase->supplier }}</div>
                <div class="col-md-4"><b>Tanggal:</b> {{ $purchase->tanggal }}</div>
                <div class="col-md-4"><b>User:</b> {{ $purchase->user->name ?? '-' }}</div>
            </div>
            <div class="mb-2"><b>Keterangan:</b> {{ $purchase->keterangan ?? '-' }}</div>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-end">
                <b>Total: Rp {{ number_format($purchase->total, 0, ',', '.') }}</b>
            </div>
        </div>
    </div>
    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection 