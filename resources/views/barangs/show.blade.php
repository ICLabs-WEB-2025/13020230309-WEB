@extends('layouts.app')
@section('title', 'Detail Barang')
@section('content')
<div class="container">
    <h3 class="mb-4">Detail Barang</h3>
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Barcode</dt>
                <dd class="col-sm-9">{{ $barang->barcode }}</dd>
                <dt class="col-sm-3">Nama</dt>
                <dd class="col-sm-9">{{ $barang->nama }}</dd>
                <dt class="col-sm-3">Deskripsi</dt>
                <dd class="col-sm-9">{{ $barang->deskripsi }}</dd>
                <dt class="col-sm-3">Kategori</dt>
                <dd class="col-sm-9">{{ $barang->kategori->nama ?? '-' }}</dd>
                <dt class="col-sm-3">SN Type</dt>
                <dd class="col-sm-9">{{ $barang->sn_type }}</dd>
                <dt class="col-sm-3">Stock</dt>
                <dd class="col-sm-9">{{ $barang->stock }}</dd>
                <dt class="col-sm-3">Satuan</dt>
                <dd class="col-sm-9">{{ $barang->satuan->nama ?? '-' }}</dd>
                <dt class="col-sm-3">Harga Umum</dt>
                <dd class="col-sm-9">Rp {{ number_format($barang->harga_umum, 0, ',', '.') }}</dd>
            </dl>
            <a href="{{ route('barangs.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('barangs.edit', $barang) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>
</div>
@endsection 