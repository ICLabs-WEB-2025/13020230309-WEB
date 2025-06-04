{{--
    edit.blade.php (Units)
    Halaman form edit satuan produk.
    Fitur:
    - Form edit nama dan deskripsi satuan
    - Tombol update satuan
--}}
@extends('layouts.app')

@section('title', 'Edit Satuan/Unit')

@section('content')
<div class="container">
    <h3 class="mb-4">Edit Satuan/Unit</h3>
    <form action="{{ route('units.update', $unit) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nama Satuan</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $unit->name }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="description" name="description">{{ $unit->description }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('units.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection 