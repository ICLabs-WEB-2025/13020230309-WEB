@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard <b>Pusat Lamongan</b></h1>

    <!-- Kartu Penjualan Hari Ini & Invoice Hari Ini -->
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="card shadow h-100" style="background: #16b3c6; color: #fff;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div style="font-size:2rem;font-weight:bold;">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
                        <div>Penjualan <b>Hari ini</b></div>
                    </div>
                    <div style="font-size:3rem;opacity:0.2;">
                        <i class="fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow h-100" style="background: #ffc107; color: #222;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div style="font-size:2rem;font-weight:bold;">{{ $totalInvoicesMonth }}</div>
                        <div>Invoice Penjualan Cash <b>Hari ini</b></div>
                    </div>
                    <div style="font-size:3rem;opacity:0.2;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Bulanan -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow h-100 p-3 d-flex flex-row align-items-center">
                <div style="font-size:2rem;font-weight:bold;">{{ $totalItemsSoldMonth }}</div>
                <div class="ml-3">
                    <div><b>Total</b> Barang Terjual Bulan Ini</div>
                </div>
                <div class="ml-auto" style="font-size:2rem;color:#16b3c6;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow h-100 p-3 d-flex flex-row align-items-center">
                <div style="font-size:2rem;font-weight:bold;">{{ $totalProducts }}</div>
                <div class="ml-3">
                    <div>Jumlah Barang</div>
                </div>
                <div class="ml-auto" style="font-size:2rem;color:#16b3c6;">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow h-100 p-3 d-flex flex-row align-items-center">
                <div style="font-size:2rem;font-weight:bold;">{{ $totalInvoicesMonth }}</div>
                <div class="ml-3">
                    <div><b>Total</b> Invoice Penjualan Bulan ini</div>
                </div>
                <div class="ml-auto" style="font-size:2rem;color:#e74c3c;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Data Barang Terlaris -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Barang <b>Terlaris</b></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode Barang</th>
                                    <th>Nama</th>
                                    <th>Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $i => $product)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td><b>{{ $product->total_sold }}</b></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Data Stok Terkecil -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Stok <b>Terkecil</b></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode Barang</th>
                                    <th>Nama</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowestStockProducts as $i => $product)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->stock }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 