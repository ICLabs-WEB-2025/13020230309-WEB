{{--
    invoice.blade.php
    Halaman untuk menampilkan dan mencetak invoice/faktur transaksi kasir.
    Fitur:
    - Menampilkan detail transaksi (kasir, pembeli, produk, total, diskon, bayar, kembali)
    - Tombol print invoice (hanya area invoice yang dicetak)
    - CSS khusus print agar hanya invoice yang tercetak
--}}
@extends('layouts.app')
@section('title', 'Invoice')

{{-- CSS khusus untuk mode print agar hanya #invoice-area yang dicetak --}}
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #invoice-area, #invoice-area * {
        visibility: visible !important;
    }
    #invoice-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: white;
        z-index: 9999;
    }
    /* Sembunyikan tombol print dan kembali saat print */
    .btn, .alert, .sidebar, .navbar, .logout {
        display: none !important;
    }
}
</style>

@section('content') 
{{-- Area utama invoice yang akan dicetak --}}
<div id="invoice-area">
    <div class="container">
        {{-- Notifikasi info untuk user --}}
        <div class="alert alert-info mb-4">
            <b>Note:</b> Halaman ini telah ditingkatkan untuk dicetak. Klik tombol cetak di bagian bawah faktur.
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        {{-- Info toko dan kasir --}}
                        <h5>No. Invoice: {{ $transaction->id }}</h5>
                        <div><b>Dari</b></div>
                        <div>Kasir Toko</div>
                        <div>Jl. Contoh No. 123</div>
                        <div>Telp: 08123456789</div>
                        <div>Email: info@kasirtoko.com</div>
                        <div>Kasir: {{ auth()->user()->name ?? '-' }}</div>
                    </div>
                    <div class="text-end">
                        {{-- Info pembeli dan transaksi --}}
                        <div><b>Pembeli</b></div>
                        <div>{{ $transaction->customer }}</div>
                        <div>Tipe Pembayaran: {{ ucfirst($transaction->payment_type) }}</div>
                        <div>Tanggal: {{ $transaction->created_at->format('d M Y H:i:s') }}</div>
                    </div>
                </div>
                <hr>
                {{-- Tabel daftar produk yang dibeli --}}
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
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
                        {{-- Tabel ringkasan pembayaran --}}
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
                {{-- Tombol aksi (kembali dan print) --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('kasir.index') }}" class="btn btn-secondary">Kembali Transaksi</a>
                    <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
