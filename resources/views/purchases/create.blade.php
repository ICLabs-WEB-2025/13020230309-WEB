@extends('layouts.app')
@section('title', 'Tambah Pembelian')
@section('content')
<div class="container">
    <h3 class="mb-4">Tambah Pembelian Produk</h3>
    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Supplier</label>
                <input type="text" name="supplier" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control">
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="position-relative">
                                    <input type="text" name="items[0][product_name]" class="form-control product-search" required>
                                    <input type="hidden" name="items[0][product_id]" class="product-id">
                                    <div class="product-suggestions position-absolute w-100 bg-white border rounded-bottom" style="display: none; z-index: 1000;"></div>
                                </div>
                            </td>
                            <td><input type="number" name="items[0][qty]" class="form-control qty" min="1" value="1" required></td>
                            <td><input type="number" name="items[0][price]" class="form-control price" min="0" value="0" required></td>
                            <td class="subtotal">0</td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-success" id="addRow">Tambah Produk</button>
            </div>
        </div>
        <div class="mb-3 text-end">
            <button type="submit" class="btn btn-primary">Simpan Pembelian</button>
        </div>
    </form>
</div>
@push('scripts')
<script>
let rowIdx = 1;
document.getElementById('addRow').onclick = function() {
    let table = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
    let newRow = table.rows[0].cloneNode(true);
    Array.from(newRow.querySelectorAll('input')).forEach(function(el) {
        if (el.type === 'number') el.value = (el.classList.contains('qty') ? 1 : 0);
        if (el.type === 'text') el.value = '';
        if (el.type === 'hidden') el.value = '';
        el.name = el.name.replace(/items\[\d+\]/, 'items['+rowIdx+']');
    });
    newRow.querySelector('.subtotal').innerText = '0';
    table.appendChild(newRow);
    rowIdx++;
};

document.getElementById('itemsTable').addEventListener('input', function(e) {
    if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
        let row = e.target.closest('tr');
        let qty = parseInt(row.querySelector('.qty').value) || 0;
        let price = parseInt(row.querySelector('.price').value) || 0;
        row.querySelector('.subtotal').innerText = (qty * price).toLocaleString('id-ID');
    }
});

document.getElementById('itemsTable').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
        let table = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
        if (table.rows.length > 1) e.target.closest('tr').remove();
    }
});

// Product search functionality
document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('product-search')) {
        document.querySelectorAll('.product-suggestions').forEach(el => el.style.display = 'none');
    }
});

document.getElementById('itemsTable').addEventListener('input', function(e) {
    if (e.target.classList.contains('product-search')) {
        const searchTerm = e.target.value;
        const suggestionsDiv = e.target.nextElementSibling.nextElementSibling;
        
        if (searchTerm.length < 2) {
            suggestionsDiv.style.display = 'none';
            return;
        }

        fetch(`/api/products/search?q=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(products => {
                suggestionsDiv.innerHTML = '';
                products.forEach(product => {
                    const div = document.createElement('div');
                    div.className = 'p-2 border-bottom product-suggestion';
                    div.style.cursor = 'pointer';
                    div.textContent = `${product.name} (${product.code})`;
                    div.onclick = function() {
                        e.target.value = product.name;
                        e.target.nextElementSibling.value = product.id;
                        suggestionsDiv.style.display = 'none';
                    };
                    suggestionsDiv.appendChild(div);
                });
                suggestionsDiv.style.display = products.length ? 'block' : 'none';
            });
    }
});
</script>
@endpush
@endsection 