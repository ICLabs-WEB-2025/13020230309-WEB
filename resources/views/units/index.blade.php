{{--
    index.blade.php (Units)
    Halaman daftar satuan produk.
    Fitur:
    - Tabel daftar satuan
    - Link ke tambah dan edit satuan
--}}
@extends('layouts.app')

@section('title', 'Daftar Satuan/Unit')

@section('content')
<div class="container">
    <h3 class="mb-4">Daftar Satuan/Unit</h3>
    <a href="{{ route('units.create') }}" class="btn btn-primary mb-3">Tambah Satuan</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Satuan</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($units as $unit)
            <tr>
                <td>{{ $unit->name }}</td>
                <td>{{ $unit->description }}</td>
                <td>
                    <a href="{{ route('units.edit', $unit) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('units.destroy', $unit) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus satuan?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 