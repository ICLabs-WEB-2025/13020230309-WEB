<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir Toko')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: #fff;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }
        .sidebar .sidebar-header {
            padding: 1.5rem 1rem 1rem 1rem;
            border-bottom: 1px solid #495057;
            text-align: center;
        }
        .sidebar .sidebar-header img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-bottom: 0.5rem;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #495057;
            color: #fff;
        }
        .sidebar .nav-icon {
            width: 1.5rem;
            display: inline-block;
        }
        .main-content {
            margin-left: 240px;
            padding: 2rem 1rem 1rem 1rem;
        }
        @media (max-width: 768px) {
            .sidebar { position: relative; width: 100%; min-height: auto; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
        <div class="sidebar d-flex flex-column">
            <div class="sidebar-header">
                <div class="mb-2">
                    <img src="https://ui-avatars.com/api/?name=Kasir&background=16b3c6&color=fff" alt="Kasir">
                </div>
                <div style="font-weight:bold;">Kasir</div>
                <div style="font-size:0.9rem; color:#adb5bd;">POS - Inventory</div>
            </div>
            <nav class="nav flex-column mt-3">
                <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
                    <span class="nav-icon"><i class="fas fa-home"></i></span> Dashboard
                </a>
                <a class="nav-link @if(request()->is('kasir*')) active @endif" href="{{ route('kasir.index') }}">
                    <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span> Penjualan
                </a>
                <!-- Master Data Dropdown -->
                <a class="nav-link" data-bs-toggle="collapse" href="#masterDataMenu" role="button" aria-expanded="false" aria-controls="masterDataMenu">
                    <span class="nav-icon"><i class="fas fa-database"></i></span> Master Data <i class="fas fa-caret-down float-end"></i>
                </a>
                <div class="collapse ms-3 @if(request()->is('products*') || request()->is('categories*') || request()->is('units*')) show @endif" id="masterDataMenu">
                    <a class="nav-link @if(request()->is('products*')) active @endif" href="{{ route('products.index') }}">
                        <span class="nav-icon"><i class="fas fa-box"></i></span> Data Produk
                    </a>
                    <a class="nav-link @if(request()->is('categories*')) active @endif" href="{{ route('categories.index') }}">
                        <span class="nav-icon"><i class="fas fa-tags"></i></span> Data Kategori
                    </a>
                    <a class="nav-link @if(request()->is('units*')) active @endif" href="{{ route('units.index') }}">
                        <span class="nav-icon"><i class="fas fa-balance-scale"></i></span> Data Satuan
                    </a>
                </div>
                <!-- End Master Data Dropdown -->
                <a class="nav-link @if(request()->is('purchases*')) active @endif" href="{{ route('purchases.index') }}">
                    <span class="nav-icon"><i class="fas fa-truck"></i></span> Pembelian
                </a>
                <a class="nav-link @if(request()->is('stock-opname*')) active @endif" href="#">
                    <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Stock Opname
                </a>
                <a class="nav-link @if(request()->is('laporan*')) active @endif" href="#">
                    <span class="nav-icon"><i class="fas fa-file-alt"></i></span> Laporan
                </a>
            </nav>
            <div class="mt-auto p-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    @endauth

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html> 