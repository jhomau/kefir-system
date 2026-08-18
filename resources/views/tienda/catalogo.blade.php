@extends('tienda.layout')

@section('title', 'Catálogo — Kefir')

@section('content')
    <h1>Catálogo de kefir</h1>
    <p style="color:#6b7280;margin-bottom:1rem">Productos disponibles para pedido web.</p>

    @forelse($productos as $producto)
        <div class="card">
            <h3 style="margin:0 0 .25rem">{{ $producto->nombre }}</h3>
            <p style="margin:0 0 .5rem;color:#6b7280">{{ $producto->descripcion }}</p>
            <p class="price">Bs. {{ number_format($producto->precio_venta, 2) }} / {{ $producto->unidad_medida }}</p>
            <p style="font-size:.85rem;color:#6b7280">Stock: {{ number_format($producto->stock, 2) }}</p>
            <form method="POST" action="{{ route('tienda.agregar', $producto) }}" style="margin-top:.75rem;display:flex;gap:.5rem;align-items:center">
                @csrf
                <input type="number" name="cantidad" value="1" min="0.001" step="0.001" style="width:90px;margin:0">
                <button class="btn" type="submit">Agregar</button>
            </form>
        </div>
    @empty
        <div class="card">No hay productos disponibles en este momento.</div>
    @endforelse
@endsection
