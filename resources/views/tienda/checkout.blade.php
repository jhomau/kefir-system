@extends('tienda.layout')

@section('title', 'Checkout — Kefir')

@section('content')
    <h1>Confirmar pedido</h1>

    <div class="card">
        <strong>Total: Bs. {{ number_format($total, 2) }}</strong>
    </div>

    <form method="POST" action="{{ route('tienda.confirmar') }}" class="card">
        @csrf
        <label>Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $cliente?->nombre) }}" required>

        <label>Teléfono / WhatsApp</label>
        <input type="text" name="telefono" value="{{ old('telefono', $cliente?->telefono) }}" required>

        <label>Dirección de entrega</label>
        <textarea name="direccion" rows="2">{{ old('direccion', $cliente?->direccion) }}</textarea>

        <label>Notas</label>
        <textarea name="notas" rows="2">{{ old('notas') }}</textarea>

        <button class="btn" type="submit">Realizar pedido</button>
    </form>
@endsection
