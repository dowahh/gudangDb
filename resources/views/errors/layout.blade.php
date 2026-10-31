<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Error') - Inventaris Gudang</title>
    {{-- font di-link langsung, tidak lewat npm --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@800&family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
    <main class="error-shell">
        <div class="error-card">
            <div class="pita-hazard"></div>
            <div class="error-body">
                <p class="error-code">@yield('code')</p>
                <h1 class="error-title">@yield('error-title')</h1>
                <p class="error-msg">@yield('message')</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali ke dashboard</a>
            </div>
        </div>
    </main>
</body>
</html>
