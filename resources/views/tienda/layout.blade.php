<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tienda Kefir')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; margin: 0; background: #fffbeb; color: #1f2937; }
        header { background: #d97706; color: white; padding: 1rem; }
        header a { color: white; text-decoration: none; margin-right: 1rem; }
        main { max-width: 720px; margin: 0 auto; padding: 1rem; }
        .card { background: white; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .btn { display: inline-block; background: #d97706; color: white; padding: .65rem 1rem; border-radius: 8px; text-decoration: none; border: 0; cursor: pointer; }
        .btn-secondary { background: #6b7280; }
        .alert { padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        input, textarea { width: 100%; padding: .65rem; margin: .35rem 0 .75rem; border: 1px solid #d1d5db; border-radius: 8px; }
        label { font-size: .9rem; font-weight: 600; }
        .price { font-weight: 700; color: #b45309; }
    </style>
</head>
<body>
    <header>
        <strong>Kefir System</strong>
        <div style="margin-top:.5rem">
            <a href="{{ route('tienda.catalogo') }}">Catálogo</a>
            <a href="{{ route('tienda.carrito') }}">Carrito</a>
        </div>
    </header>
    <main>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
