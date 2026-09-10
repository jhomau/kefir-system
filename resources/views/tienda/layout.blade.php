<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $ajustes->nombre_negocio)</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; margin: 0; background: #fffbeb; color: #1f2937; min-height: 100vh; display: flex; flex-direction: column; }
        header { background: #d97706; color: white; padding: 1rem; }
        header a { color: white; text-decoration: none; margin-right: 1rem; }
        header .eslogan { opacity: .9; font-size: .85rem; margin-top: .2rem; }
        main { max-width: 720px; margin: 0 auto; padding: 1rem; width: 100%; flex: 1; }
        footer { background: #fef3c7; color: #92400e; padding: 1rem; font-size: .85rem; }
        footer .inner { max-width: 720px; margin: 0 auto; display: grid; gap: .25rem; }
        footer a { color: #92400e; }
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
        <strong>{{ $ajustes->nombre_negocio }}</strong>
        @if($ajustes->eslogan)
            <div class="eslogan">{{ $ajustes->eslogan }}</div>
        @endif
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
    @if($ajustes->telefono || $ajustes->whatsapp || $ajustes->correo || $ajustes->direccion || $ajustes->horario)
        <footer>
            <div class="inner">
                @if($ajustes->horario)
                    <div>{{ $ajustes->horario }}</div>
                @endif
                @if($ajustes->direccion)
                    <div>{{ $ajustes->direccion }}</div>
                @endif
                @if($ajustes->telefono)
                    <div>Tel: <a href="tel:{{ $ajustes->telefono }}">{{ $ajustes->telefono }}</a></div>
                @endif
                @if($ajustes->whatsapp)
                    <div>WhatsApp:
                        @if($ajustes->enlaceWhatsapp())
                            <a href="{{ $ajustes->enlaceWhatsapp() }}" target="_blank" rel="noopener">{{ $ajustes->whatsapp }}</a>
                        @else
                            {{ $ajustes->whatsapp }}
                        @endif
                    </div>
                @endif
                @if($ajustes->correo)
                    <div><a href="mailto:{{ $ajustes->correo }}">{{ $ajustes->correo }}</a></div>
                @endif
            </div>
        </footer>
    @endif
</body>
</html>
