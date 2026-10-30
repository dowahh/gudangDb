<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Inventaris Gudang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">

    @auth
        {{-- Sidebar kiri --}}
        <div class="flex min-h-screen">
            <aside class="w-56 bg-slate-800 text-white flex flex-col">
                <div class="px-4 py-5 border-b border-slate-700">
                    <p class="font-semibold text-sm">Inventaris Gudang</p>
                </div>
                <nav class="flex-1 px-2 py-4 space-y-1 text-sm">
                    <a href="{{ route('dashboard') }}"
                       class="block px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-slate-700' : 'hover:bg-slate-700' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('items.index') }}"
                       class="block px-3 py-2 rounded {{ request()->routeIs('items.*') ? 'bg-slate-700' : 'hover:bg-slate-700' }}">
                        Barang
                    </a>
                    <a href="{{ route('transactions.index') }}"
                       class="block px-3 py-2 rounded {{ request()->routeIs('transactions.*') ? 'bg-slate-700' : 'hover:bg-slate-700' }}">
                        Transaksi
                    </a>
                    {{-- Menu master data tampil hanya untuk admin. Proteksi asli ada di middleware route. --}}
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('categories.index') }}"
                           class="block px-3 py-2 rounded {{ request()->routeIs('categories.*') ? 'bg-slate-700' : 'hover:bg-slate-700' }}">
                            Kategori
                        </a>
                        <a href="{{ route('suppliers.index') }}"
                           class="block px-3 py-2 rounded {{ request()->routeIs('suppliers.*') ? 'bg-slate-700' : 'hover:bg-slate-700' }}">
                            Supplier
                        </a>
                        <a href="{{ route('users.index') }}"
                           class="block px-3 py-2 rounded {{ request()->routeIs('users.*') ? 'bg-slate-700' : 'hover:bg-slate-700' }}">
                            User
                        </a>
                    @endif
                </nav>
                <div class="px-4 py-4 border-t border-slate-700 text-sm">
                    <p class="truncate">{{ auth()->user()->name }}</p>
                    <p class="text-slate-400 text-xs mb-2">{{ auth()->user()->role }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        {{-- KEAMANAN: @csrf melindungi form dari CSRF --}}
                        @csrf
                        <button type="submit" class="text-slate-300 hover:text-white underline text-xs">
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>

            <main class="flex-1 p-6">
                @if (session('status'))
                    <div class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    @else
        {{-- Halaman auth tanpa sidebar --}}
        <main class="max-w-md mx-auto mt-16 p-4">
            @if (session('status'))
                <div class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>
    @endauth

</body>
</html>
