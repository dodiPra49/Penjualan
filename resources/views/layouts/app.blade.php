<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Manajemen Penjualan')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome 5 & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            --sidebar-gradient: linear-gradient(180deg, #1e1b4b 0%, #0f172a 100%);
            --navbar-dark-bg: #1e293b;
            --card-border: rgba(226, 232, 240, 0.8);
            --bg-body: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar Styling with Bootstrap 4 */
        .navbar-custom {
            background: var(--primary-gradient);
            box-shadow: 0 4px 20px -2px rgba(79, 70, 229, 0.25);
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
            font-size: 1.3rem;
            color: #ffffff !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link:focus,
        .navbar-custom .nav-item.show .nav-link {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        .navbar-custom .nav-item.active .nav-link {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.22);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Dropdown Menu Styling */
        .dropdown-menu {
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 0.75rem;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            padding: 0.5rem;
            min-width: 220px;
            animation: fadeInDropdown 0.2s ease;
        }

        @keyframes fadeInDropdown {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-header {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 0.5rem 0.85rem 0.25rem;
        }

        .dropdown-item {
            font-weight: 600;
            font-size: 0.88rem;
            color: #475569;
            padding: 0.55rem 0.85rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.15s ease;
        }

        .dropdown-item i {
            font-size: 0.95rem;
            width: 20px;
            text-align: center;
        }

        .dropdown-item:hover, .dropdown-item:focus {
            background-color: #f1f5f9;
            color: #4f46e5;
            padding-left: 1rem;
        }

        .dropdown-item.active, .dropdown-item:active {
            background-color: #4f46e5;
            color: #ffffff !important;
        }

        .dropdown-item.active i {
            color: #ffffff !important;
        }

        .dropdown-divider {
            border-top: 1px solid #f1f5f9;
            margin: 0.4rem 0;
        }

        /* Custom Cards & UI Elements */
        .card-stat {
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            background: #ffffff;
        }

        .card-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .main-card {
            border-radius: 1rem;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }

        .table-custom th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem 1.25rem;
        }

        .table-custom td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tr:hover td {
            background-color: #f8fafc;
        }

        .badge-status-aktif {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
        }

        .badge-status-nonaktif {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
        }

        .badge-code {
            background-color: #eff6ff;
            color: #2563eb;
            font-family: monospace;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            border: 1px solid #dbeafe;
        }

        .btn-action {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.15s ease;
        }

        .btn-action:hover {
            transform: scale(1.08);
        }

        .btn-primary-gradient {
            background: var(--primary-gradient);
            border: none;
            color: white !important;
            font-weight: 600;
            padding: 0.55rem 1.25rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            transition: all 0.2s ease;
        }

        .btn-primary-gradient:hover {
            background: linear-gradient(135deg, #4338ca 0%, #2563eb 100%);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
            transform: translateY(-1px);
        }

        .modal-content {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-header {
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
        }

        .form-control:focus, .custom-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .footer-custom {
            margin-top: auto;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1.25rem 0;
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Subtly colored backgrounds */
        .bg-primary-subtle { background-color: #e0e7ff; color: #4338ca; }
        .bg-success-subtle { background-color: #dcfce7; color: #15803d; }
        .bg-danger-subtle { background-color: #fee2e2; color: #b91c1c; }
        .bg-warning-subtle { background-color: #fef3c7; color: #b45309; }
        .bg-info-subtle { background-color: #e0f2fe; color: #0369a1; }
        .bg-secondary-subtle { background-color: #f1f5f9; color: #475569; }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Navbar with Dropdown Menus (Bootstrap 4) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <!-- Brand / Logo -->
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-store-alt text-warning"></i>
                <span>Sistem Penjualan</span>
            </a>

            <!-- Mobile Hamburger Toggle -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Menu List -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mr-auto align-items-lg-center">
                    
                    <!-- 1. Master Dropdown -->
                    <li class="nav-item dropdown {{ request()->routeIs('produk.*', 'kategori.*', 'pelanggan.*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="masterDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-layer-group text-white-50"></i> Master
                        </a>
                        <div class="dropdown-menu shadow-sm" aria-labelledby="masterDropdown">
                            <div class="dropdown-header">Master Data</div>
                            <a class="dropdown-item {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
                                <i class="fas fa-box text-primary"></i> Produk
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">
                                <i class="fas fa-tags text-info"></i> Kategori
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}" href="{{ route('pelanggan.index') }}">
                                <i class="fas fa-users text-success"></i> Pelanggan
                            </a>
                        </div>
                    </li>

                    <!-- 2. Transaksi Dropdown -->
                    <li class="nav-item dropdown {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="transaksiDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-shopping-cart text-white-50"></i> Transaksi
                        </a>
                        <div class="dropdown-menu shadow-sm" aria-labelledby="transaksiDropdown">
                            <div class="dropdown-header">Menu Transaksi</div>
                            <a class="dropdown-item {{ request()->routeIs('transaksi.penjualan') ? 'active' : '' }}" href="{{ route('transaksi.penjualan') }}">
                                <i class="fas fa-cash-register text-success"></i> Penjualan
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('transaksi.retur') ? 'active' : '' }}" href="{{ route('transaksi.retur') }}">
                                <i class="fas fa-undo-alt text-danger"></i> Retur Penjualan
                            </a>
                        </div>
                    </li>

                    <!-- 3. Laporan Dropdown -->
                    <li class="nav-item dropdown {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="laporanDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-chart-bar text-white-50"></i> Laporan
                        </a>
                        <div class="dropdown-menu shadow-sm" aria-labelledby="laporanDropdown">
                            <div class="dropdown-header">Laporan & Rekap</div>
                            <a class="dropdown-item {{ request()->routeIs('laporan.penjualan') ? 'active' : '' }}" href="{{ route('laporan.penjualan') }}">
                                <i class="fas fa-file-invoice-dollar text-primary"></i> Penjualan
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('laporan.pelanggan') ? 'active' : '' }}" href="{{ route('laporan.pelanggan') }}">
                                <i class="fas fa-file-alt text-info"></i> Pelanggan
                            </a>
                        </div>
                    </li>

                    <!-- 4. Tool Dropdown -->
                    <li class="nav-item dropdown {{ request()->routeIs('home', 'dashboard.*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="toolDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-tools text-white-50"></i> Tool
                        </a>
                        <div class="dropdown-menu shadow-sm" aria-labelledby="toolDropdown">
                            <div class="dropdown-header">Alat & Utilitas</div>
                            <a class="dropdown-item {{ request()->routeIs('home', 'dashboard.*') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-tachometer-alt text-warning"></i> Dashboard Penjualan
                            </a>
                        </div>
                    </li>
                </ul>

                <!-- Right Side Status Badge -->
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item d-none d-lg-block">
                        <span class="badge badge-light text-primary px-3 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-check-circle text-success mr-1"></i> Bootstrap 4 Active
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-custom text-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-left">
                    &copy; 2026 <strong>Sistem Penjualan</strong> - Bootstrap 4 & Laravel Framework
                </div>
                <div class="col-md-6 text-md-right text-muted mt-2 mt-md-0">
                    <span class="badge badge-primary mr-1">Laravel 10</span>
                    <span class="badge badge-info mr-1">Bootstrap 4.6</span>
                    <span class="badge badge-success">REST API Ready</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery, Popper.js, and Bootstrap 4 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap 4 / 5 Compatibility Helpers -->
    <script>
        // Modal & Collapse interoperability helper for child views
        if (typeof window.bootstrap === 'undefined') {
            window.bootstrap = {
                Modal: function(element) {
                    this.element = element;
                    this.show = function() { $(this.element).modal('show'); };
                    this.hide = function() { $(this.element).modal('hide'); };
                }
            };
        }

        $(document).on('click', '[data-bs-dismiss="modal"]', function() {
            $(this).closest('.modal').modal('hide');
        });

        $(document).on('click', '[data-bs-toggle="collapse"]', function() {
            var target = $(this).attr('data-bs-target');
            if (target) {
                $(target).collapse('toggle');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>