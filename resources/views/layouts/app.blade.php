<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Inventaris Gudang</title>
    {{-- font di-link langsung, tidak lewat npm --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@800&family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    @auth
        <div class="mobile-topbar">
            <span class="brand">Gudang</span>
            <button type="button" class="menu-toggle" id="menu-toggle">Menu</button>
        </div>
        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

        <div class="app-shell">
            <aside class="sidebar" id="sidebar">
                <div class="pita-hazard"></div>
                <div class="sidebar-brand">
                    <span class="sidebar-brand-mark" aria-hidden="true"></span>
                    <div>
                        Gudang
                        <span>Inventaris · v1</span>
                    </div>
                </div>
                <nav class="sidebar-nav">
                    <p class="nav-label">Operasional</p>
                    <a href="{{ route('dashboard') }}"
                       class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('items.index') }}"
                       class="{{ request()->routeIs('items.*') ? 'active' : '' }}">
                        Barang
                    </a>
                    <a href="{{ route('transactions.index') }}"
                       class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        Transaksi
                    </a>

                    @if (auth()->user()->isAdmin())
                        <p class="nav-label nav-label-gap">Administrasi</p>
                        <a href="{{ route('categories.index') }}"
                           class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            Kategori
                        </a>
                        <a href="{{ route('suppliers.index') }}"
                           class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            Supplier
                        </a>
                        <a href="{{ route('users.index') }}"
                           class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                            User
                        </a>
                        <a href="{{ route('reports.stock') }}"
                           class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            Laporan
                        </a>
                    @endif
                </nav>
                <div class="sidebar-footer">
                    <p class="name">{{ auth()->user()->name }}</p>
                    <p class="role">
                        <span class="stempel stempel-aman stempel-role">{{ auth()->user()->role }}</span>
                    </p>
                    <form method="POST" action="{{ route('logout') }}">
                        {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                        @csrf
                        <button type="submit">Keluar</button>
                    </form>
                </div>
            </aside>

            <main class="main">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    @else
        <div class="auth-shell">
            <aside class="auth-aside">
                <div class="auth-aside-top">
                    <div class="auth-aside-logo">Gudang <span>v1</span></div>
                    <div class="auth-aside-meta">Inventaris Gudang</div>
                </div>

                <h1 class="auth-aside-title">Stok<br><em>Terkendali.</em></h1>
                <p class="auth-aside-desc">
                    Catat barang masuk dan keluar, pantau stok menipis, kelola gudang dalam satu sistem.
                </p>

                <div class="auth-aside-bottom">
                    {{-- label paket hiasan: HTML + CSS murni, bukan gambar --}}
                    <div class="label-paket">
                        <div class="label-paket-head">
                            <span>Label Paket</span>
                            <span>GUD-01</span>
                        </div>
                        <div class="label-paket-rows">
                            <div><span class="k">SKU</span><span class="v">PKG-001</span></div>
                            <div><span class="k">Barang</span><span class="v">Kardus 40x30</span></div>
                            <div><span class="k">Qty</span><span class="v">47</span></div>
                        </div>
                        <span class="stempel stempel-aman">Stok Aman</span>
                    </div>
                </div>

                <div class="pita-hazard"></div>
            </aside>

            <main class="auth-main">
                <div>
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    @endauth

</body>
</html>
