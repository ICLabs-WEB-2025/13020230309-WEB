{{--
    create.blade.php (Purchases)
    Halaman form tambah pembelian barang dari supplier.
    Fitur:
    - Form input supplier, tanggal, keterangan
    - Tabel input produk dan qty
    - Tombol simpan pembelian
--}}
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
                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control">
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="product-search" class="form-control" placeholder="Cari produk...">
                            <button type="button" class="btn btn-outline-primary" id="btn-search">Cari</button>
                        </div>
                        <div id="search-results" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></div>
                    </div>
                </div>
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
                        <!-- Items will be added here dynamically -->
                    </tbody>
                </table>
                <div class="text-end mb-3">
                    <strong>Total: <span id="total-amount">Rp 0</span></strong>
                </div>
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
$(document).ready(function() {
    let rowIdx = 0;
    const products = @json($products);
    
    // Function to format currency
    function formatCurrency(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }
    
    // Function to calculate subtotal
    function calculateSubtotal(row) {
        const qty = parseInt(row.find('.qty').val()) || 0;
        const price = parseFloat(row.find('.price').val()) || 0;
        const subtotal = qty * price;
        row.find('.subtotal').text(formatCurrency(subtotal));
        calculateTotal();
    }
    
    // Function to calculate total
    function calculateTotal() {
        let total = 0;
        $('#itemsTable tbody tr').each(function() {
            const qty = parseInt($(this).find('.qty').val()) || 0;
            const price = parseFloat($(this).find('.price').val()) || 0;
            total += qty * price;
        });
        $('#total-amount').text(formatCurrency(total));
    }
    
    // Product search functionality
    $('#product-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        if (searchTerm.length < 2) {
            $('#search-results').hide();
            return;
        }
        
        const filteredProducts = products.filter(product => 
            product.name.toLowerCase().includes(searchTerm) || 
            product.code.toLowerCase().includes(searchTerm)
        );
        
        if (filteredProducts.length > 0) {
            let html = '';
            filteredProducts.forEach(product => {
                html += `
                    <a href="#" class="list-group-item list-group-item-action product-item" 
                       data-id="${product.id}"
                       data-name="${product.name}"
                       data-code="${product.code}"
                       data-price="${product.price}">
                        ${product.code} - ${product.name} (${formatCurrency(product.price)})
                    </a>
                `;
            });
            $('#search-results').html(html).show();
        } else {
            $('#search-results').hide();
        }
    });
    
    // Handle product selection
    $(document).on('click', '.product-item', function(e) {
        e.preventDefault();
        const product = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            code: $(this).data('code'),
            price: $(this).data('price')
        };
        
        addProductToTable(product);
        $('#product-search').val('');
        $('#search-results').hide();
    });
    
    // Function to add product to table
    function addProductToTable(product) {
        const row = `
            <tr>
                <td>
                    ${product.name}
                    <input type="hidden" name="items[${rowIdx}][product_id]" value="${product.id}">
                </td>
                <td>
                    <input type="number" name="items[${rowIdx}][qty]" class="form-control qty" min="1" value="1" required>
                </td>
                <td>
                    <input type="number" name="items[${rowIdx}][price]" class="form-control price" min="0" value="${product.price}" required>
                </td>
                <td class="subtotal">${formatCurrency(product.price)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                </td>
            </tr>
        `;
        $('#itemsTable tbody').append(row);
        rowIdx++;
    }
    
    // Handle quantity and price changes
    $(document).on('input', '.qty, .price', function() {
        calculateSubtotal($(this).closest('tr'));
    });
    
    // Handle row removal
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });
    
    // Handle form submission
    $('#purchaseForm').on('submit', function(e) {
        if ($('#itemsTable tbody tr').length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal satu produk!');
        }
    });
});
</script>
@endpush
@endsection 