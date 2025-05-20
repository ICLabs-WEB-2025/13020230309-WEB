@extends('layouts.app')
@section('title', 'Invoice')

@section('content')
<div class="container">
    <div class="alert alert-info mb-4">
        <b>Note:</b> Halaman ini telah ditingkatkan untuk dicetak. Klik tombol cetak di bagian bawah faktur.
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h5>No. Invoice: {{ $transaction->id }}</h5>
                    <div><b>Dari</b></div>
                    <div>Kasir Toko</div>
                    <div>Jl. Contoh No. 123</div>
                    <div>Telp: 08123456789</div>
                    <div>Email: info@kasirtoko.com</div>
                    <div>Kasir: {{ auth()->user()->name ?? '-' }}</div>
                </div>
                <div class="text-end">
                    <div><b>Pembeli</b></div>
                    <div>{{ $transaction->customer }}</div>
                    <div>Tipe Pembayaran: {{ ucfirst($transaction->payment_type) }}</div>
                    <div>Tanggal: {{ $transaction->created_at->format('d M Y H:i:s') }}</div>
                </div>
            </div>
            <hr>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row mt-4">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Sub Total:</th>
                            <td class="text-end">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Diskon:</th>
                            <td class="text-end">Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Total:</th>
                            <td class="text-end">Rp {{ number_format($transaction->total - $transaction->discount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Bayar:</th>
                            <td class="text-end">Rp {{ number_format($transaction->paid, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Kembali:</th>
                            <td class="text-end">Rp {{ number_format($transaction->change, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('kasir.index') }}" class="btn btn-secondary">Kembali Transaksi</a>
                <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
            </div>
        </div>
    </div>
</div>
@endsection
