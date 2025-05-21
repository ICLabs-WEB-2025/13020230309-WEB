@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('content')
<div class="container">
    <h3 class="mb-4">Tambah Barang</h3>
    <form action="{{ route('barangs.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Barcode</label>
            <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror" value="{{ old('barcode') }}">
            @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}">
            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label>Kategori</label>
            <select name="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror">
                <option value="">Pilih Kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" @if(old('kategori_id') == $kategori->id) selected @endif>{{ $kategori->nama }}</option>
                @endforeach
            </select>
            @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label>SN Type</label>
            <select name="sn_type" class="form-select @error('sn_type') is-invalid @enderror">
                <option value="">Pilih Tipe</option>
                <option value="SN" @if(old('sn_type') == 'SN') selected @endif>SN</option>
                <option value="non-SN" @if(old('sn_type') == 'non-SN') selected @endif>non-SN</option>
            </select>
            @error('sn_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}">
            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label>Satuan</label>
            <select name="satuan_id" class="form-select @error('satuan_id') is-invalid @enderror">
                <option value="">Pilih Satuan</option>
                @foreach($satuans as $satuan)
                    <option value="{{ $satuan->id }}" @if(old('satuan_id') == $satuan->id) selected @endif>{{ $satuan->nama }}</option>
                @endforeach
            </select>
            @error('satuan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label>Harga Umum</label>
            <input type="number" name="harga_umum" class="form-control @error('harga_umum') is-invalid @enderror" value="{{ old('harga_umum', 0) }}">
            @error('harga_umum')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('barangs.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection 