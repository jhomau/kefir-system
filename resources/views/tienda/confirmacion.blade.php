@extends('tienda.layout')

@section('title', 'Pedido confirmado')

@section('content')
    <div class="card">
        <h1 style="margin-top:0">¡Pedido registrado!</h1>
        <p>Tu pedido <strong>{{ $venta->numero_venta }}</strong> fue recibido correctamente.</p>
        <p>Total: <span class="price">Bs. {{ number_format($venta->total, 2) }}</span></p>
        <p style="color:#6b7280">Nos comunicaremos contigo para coordinar la entrega.</p>
        <a class="btn" href="{{ route('tienda.catalogo') }}">Volver al catálogo</a>
    </div>
@endsection
