@extends('tienda.layout')

@section('title', 'Carrito — Kefir')

@section('content')
    <h1>Tu carrito</h1>

    @forelse($items as $item)
        <div class="card" style="display:flex;justify-content:space-between;align-items:center;gap:1rem">
            <div>
                <strong>{{ $item['nombre'] }}</strong>
                <div>Cantidad: {{ $item['cantidad'] }} × Bs. {{ number_format($item['precio'], 2) }}</div>
            </div>
            <form method="POST" action="{{ route('tienda.quitar', $item['producto_id']) }}">
                @csrf
                <button class="btn btn-secondary" type="submit">Quitar</button>
            </form>
        </div>
    @empty
        <div class="card">Tu carrito está vacío.</div>
    @endforelse

    @if(count($items))
        <div class="card">
            <strong>Total: Bs. {{ number_format($total, 2) }}</strong>
            <div style="margin-top:1rem">
                <a class="btn" href="{{ route('tienda.checkout') }}">Confirmar pedido</a>
            </div>
        </div>
    @endif
@endsection