<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Udara Jabodetabek</title>
    <link rel="stylesheet" href="{{ asset('css/udara.css') }}">
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a href="{{ route('dashboard') }}" class="brand">Udara Jabodetabek</a>
            <nav aria-label="Menu utama">
                <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Dashboard</a>
                <a href="{{ route('lokasi.index') }}" @class(['active' => request()->routeIs('lokasi.*')])>Lokasi</a>
            </nav>
        </div>
    </header>

    <main class="page">
        @if (session('success'))
            <p class="notice notice-ok" role="status">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div class="notice notice-error" role="alert">
                <p>Data belum bisa disimpan:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html> 