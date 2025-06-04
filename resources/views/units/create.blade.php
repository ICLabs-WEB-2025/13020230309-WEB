{{--
    create.blade.php (Units)
    Halaman form tambah satuan produk.
    Fitur:
    - Form input nama dan deskripsi satuan
    - Tombol simpan satuan
--}}
@extends('layouts.app')

@section('title', 'Tambah Satuan/Unit')

@section('content')
<div class="container">
    <h3 class="mb-4">Tambah Satuan/Unit</h3>
    <form action="{{ route('units.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Satuan</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('units.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection 