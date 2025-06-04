{{--
    index.blade.php (Kasir)
    Halaman utama transaksi kasir.
    Fitur:
    - Input produk ke keranjang (search barcode/kode/nama)
    - Pilih customer dan tipe pembayaran
    - Tabel keranjang belanja
    - Input diskon, bayar, dan hitung kembali
    - Simpan transaksi dan redirect ke invoice
    - Script JS untuk handle keranjang dan AJAX
--}}
@extends('layouts.app')
@section('title', 'Kasir')

@section('content')
<div class="container">
    {{-- Header halaman --}}
    <h3 class="mb-4">Transaksi Kasir</h3>
    {{-- Form transaksi kasir --}}
    <form id="kasirForm">
        <div class="row mb-3">
            <div class="col-md-6">
                {{-- Input pencarian produk --}}
                <div class="input-group">
                    <input type="text" id="search-barcode" class="form-control" placeholder="Cari barcode/kode/nama produk">
                    <button type="button" class="btn btn-outline-primary" id="btn-search">Cari</button>
                </div>
                <div id="search-results" class="list-group position-absolute w-50"></div>
            </div>
            <div class="col-md-3">
                {{-- Pilih customer --}}
                <select class="form-select" id="customer" name="customer">
                    <option value="Umum">Umum</option>
                </select>
            </div>
            <div class="col-md-3">
                {{-- Pilih tipe pembayaran --}}
                <select class="form-select" id="payment_type" name="payment_type">
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>
        </div>
        {{-- Tabel keranjang belanja --}}
        <table class="table table-bordered" id="cart-table">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Satuan</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Keranjang diisi JS -->
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-4 offset-md-8">
                {{-- Ringkasan pembayaran --}}
                <table class="table">
                    <tr>
                        <th>Total</th>
                        <td class="text-end" id="total">Rp 0</td>
                    </tr>
                    <tr>
                        <th>Diskon</th>
                        <td><input type="number" min="0" value="0" class="form-control form-control-sm" id="discount" name="discount"></td>
                    </tr>
                    <tr>
                        <th>Sub Total</th>
                        <td class="text-end" id="subtotal">Rp 0</td>
                    </tr>
                    <tr>
                        <th>Bayar</th>
                        <td><input type="number" min="0" value="0" class="form-control form-control-sm" id="paid" name="paid"></td>
                    </tr>
                    <tr>
                        <th>Kembali</th>
                        <td class="text-end" id="change">Rp 0</td>
                    </tr>
                </table>
                <button type="submit" class="btn btn-primary w-100">Simpan Payment</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Script JS untuk handle keranjang, pencarian produk, dan submit transaksi --}}
<script>
let cart = [];
function renderCart() {
    let tbody = document.querySelector('#cart-table tbody');
    tbody.innerHTML = '';
    let total = 0;
    cart.forEach((item, idx) => {
        let subtotal = item.qty * item.price;
        total += subtotal;
        tbody.innerHTML += `<tr>
            <td>${idx+1}</td>
            <td>${item.name}</td>
            <td>Rp ${parseInt(item.price).toLocaleString()}</td>
            <td>${item.unit}</td>
            <td><input type="number" min="1" value="${item.qty}" onchange="updateQty(${idx}, this.value)" class="form-control form-control-sm"></td>
            <td>Rp ${(subtotal).toLocaleString()}</td>
            <td><button type="button" onclick="removeItem(${idx})" class="btn btn-danger btn-sm">Hapus</button></td>
        </tr>`;
    });
    document.getElementById('total').textContent = 'Rp ' + total.toLocaleString();
    let discount = parseInt(document.getElementById('discount').value) || 0;
    let subtotal = total - discount;
    document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString();
    let paid = parseInt(document.getElementById('paid').value) || 0;
    let change = paid - subtotal;
    document.getElementById('change').textContent = 'Rp ' + change.toLocaleString();
}

window.updateQty = function(idx, val) {
    cart[idx].qty = parseInt(val);
    renderCart();
}

window.removeItem = function(idx) {
    cart.splice(idx, 1);
    renderCart();
}

document.getElementById('discount').addEventListener('input', renderCart);
document.getElementById('paid').addEventListener('input', renderCart);

document.getElementById('search-barcode').addEventListener('input', function() {
    let q = this.value;
    if (q.length < 2) {
        document.getElementById('search-results').innerHTML = '';
        return;
    }
    fetch(`/kasir/search?q=${q}`)
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(product => {
                html += `<button type="button" class="list-group-item list-group-item-action search-item"
                    data-id="${product.id}" data-name="${product.name}" data-price="${product.price}" data-unit="${product.unit}">
                    ${product.code} - ${product.name} (Stok: ${product.stock})
                </button>`;
            });
            document.getElementById('search-results').innerHTML = html;
            document.querySelectorAll('.search-item').forEach(item => {
                item.onclick = function() {
                    let id = this.dataset.id;
                    let name = this.dataset.name;
                    let price = this.dataset.price;
                    let unit = this.dataset.unit;
                    let found = cart.find(i => i.id == id);
                    if (found) {
                        found.qty++;
                    } else {
                        cart.push({id, name, price, unit, qty: 1});
                    }
                    renderCart();
                    document.getElementById('search-results').innerHTML = '';
                    document.getElementById('search-barcode').value = '';
                }
            });
        });
});

document.getElementById('kasirForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let items = cart.map(item => ({
        product_id: item.id,
        quantity: item.qty
    }));
    let data = {
        items: items,
        customer: document.getElementById('customer').value,
        payment_type: document.getElementById('payment_type').value,
        discount: document.getElementById('discount').value,
        paid: document.getElementById('paid').value
    };
    fetch('/kasir/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if(res.success) {
            window.location.href = '/kasir/invoice/' + res.invoice_id;
        } else {
            alert(res.message);
        }
    });
});
</script>
@endpush

{{-- CSS khusus print untuk invoice (jika ada) --}}
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
        width: 100vw;
        background: white;
        z-index: 9999;
    }
}
</style>