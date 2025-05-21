@extends('layouts.app')
@section('title', 'Data Barang')
@section('content')
<div class="container">
    <h3 class="mb-4">Data Barang</h3>
    <a href="{{ route('barangs.create') }}" class="btn btn-primary mb-3">Tambah Barang</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>SN Type</th>
                <th>Stock</th>
                <th>Satuan</th>
                <th>Harga Umum</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $barang)
            <tr>
                <td>{{ $barang->barcode }}</td>
                <td>{{ $barang->nama }}</td>
                <td>{{ $barang->deskripsi }}</td>
                <td>{{ $barang->kategori->nama ?? '-' }}</td>
                <td>{{ $barang->sn_type }}</td>
                <td>{{ $barang->stock }}</td>
                <td>{{ $barang->satuan->nama ?? '-' }}</td>
                <td>Rp {{ number_format($barang->harga_umum, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('barangs.show', $barang) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('barangs.edit', $barang) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('barangs.destroy', $barang) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $barangs->links() }}
</div>
@endsection 