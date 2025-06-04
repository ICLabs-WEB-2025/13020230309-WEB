{{--
    create.blade.php (Transactions)
    Halaman form tambah transaksi penjualan.
    Fitur:
    - Form input produk, qty, dan customer
    - Tabel keranjang transaksi
    - Tombol simpan transaksi
--}}
@extends('layouts.app')

@section('title', 'Transaksi Baru - Kasir Toko')

@section('content')
<div class="row">
    <!-- Kolom Kiri - Daftar Produk -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Daftar Produk</h5>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Cari produk..." id="searchProduct">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <div class="row row-cols-2 row-cols-md-3 g-3" id="productList">
                    @foreach($products as $product)
                    <div class="col">
                        <div class="card h-100 product-card" 
                             data-id="{{ $product->id }}"
                             data-name="{{ $product->name }}"
                             data-price="{{ $product->price }}"
                             data-stock="{{ $product->stock }}">
                            <div class="card-body">
                                <h6 class="card-title">{{ $product->name }}</h6>
                                <p class="card-text text-muted mb-1">Stok: {{ $product->stock }}</p>
                                <p class="card-text fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan - Keranjang -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Keranjang</h5>
                
                <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                    @csrf
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name">
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm" id="cartTable">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Items will be added here dynamically -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">Total:</td>
                                    <td colspan="2" class="fw-bold" id="totalAmount">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>

                    <div class="mb-3" id="cashPaymentSection">
                        <label for="cash_amount" class="form-label">Jumlah Uang</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="cash_amount" name="cash_amount">
                        </div>
                        <div class="form-text" id="changeAmount">Kembalian: Rp 0</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="checkoutBtn" disabled>
                            <i class="bi bi-cash"></i> Proses Pembayaran
                        </button>
                        <button type="button" class="btn btn-outline-danger" id="clearCartBtn">
                            <i class="bi bi-trash"></i> Kosongkan Keranjang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cart = new Map();
    const productCards = document.querySelectorAll('.product-card');
    const cartTable = document.getElementById('cartTable').getElementsByTagName('tbody')[0];
    const totalAmountElement = document.getElementById('totalAmount');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const clearCartBtn = document.getElementById('clearCartBtn');
    const cashAmountInput = document.getElementById('cash_amount');
    const changeAmountElement = document.getElementById('changeAmount');
    const paymentMethodSelect = document.getElementById('payment_method');
    const cashPaymentSection = document.getElementById('cashPaymentSection');

    // Event listener untuk produk
    productCards.forEach(card => {
        card.addEventListener('click', () => {
            const id = card.dataset.id;
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);
            const stock = parseInt(card.dataset.stock);

            if (cart.has(id)) {
                const currentQty = cart.get(id).quantity;
                if (currentQty < stock) {
                    cart.get(id).quantity++;
                    updateCart();
                } else {
                    alert('Stok tidak mencukupi!');
                }
            } else {
                cart.set(id, {
                    name: name,
                    price: price,
                    quantity: 1
                });
                updateCart();
            }
        });
    });

    // Update tampilan keranjang
    function updateCart() {
        cartTable.innerHTML = '';
        let total = 0;

        cart.forEach((item, id) => {
            const subtotal = item.price * item.quantity;
            total += subtotal;

            const row = cartTable.insertRow();
            row.innerHTML = `
                <td>${item.name}</td>
                <td>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity('${id}', -1)">-</button>
                        <input type="number" class="form-control text-center" value="${item.quantity}" 
                               onchange="updateQuantity('${id}', this.value - ${item.quantity})">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity('${id}', 1)">+</button>
                    </div>
                </td>
                <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItem('${id}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
        });

        totalAmountElement.textContent = `Rp ${total.toLocaleString('id-ID')}`;
        checkoutBtn.disabled = cart.size === 0;
        updateChange();
    }

    // Update quantity
    window.updateQuantity = function(id, change) {
        const item = cart.get(id);
        const newQty = item.quantity + change;
        const stock = parseInt(document.querySelector(`[data-id="${id}"]`).dataset.stock);

        if (newQty > 0 && newQty <= stock) {
            item.quantity = newQty;
            updateCart();
        } else if (newQty <= 0) {
            removeItem(id);
        } else {
            alert('Stok tidak mencukupi!');
        }
    };

    // Remove item
    window.removeItem = function(id) {
        cart.delete(id);
        updateCart();
    };

    // Clear cart
    clearCartBtn.addEventListener('click', () => {
        if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
            cart.clear();
            updateCart();
        }
    });

    // Update change amount
    function updateChange() {
        const total = Array.from(cart.values()).reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const cashAmount = parseFloat(cashAmountInput.value) || 0;
        const change = cashAmount - total;
        
        changeAmountElement.textContent = `Kembalian: Rp ${change.toLocaleString('id-ID')}`;
        changeAmountElement.className = change >= 0 ? 'form-text text-success' : 'form-text text-danger';
    }

    // Event listeners for payment
    cashAmountInput.addEventListener('input', updateChange);
    paymentMethodSelect.addEventListener('change', function() {
        cashPaymentSection.style.display = this.value === 'cash' ? 'block' : 'none';
    });

    // Form submission
    document.getElementById('transactionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('items', JSON.stringify(Array.from(cart.entries())));

        // Here you would typically send the data to your server
        console.log('Transaction data:', Object.fromEntries(formData));
    });
});
</script>
@endpush
@endsection 