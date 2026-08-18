<x-filament-widgets::widget>
    <x-filament::section heading="Alertas de inventario">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <h4 class="mb-2 font-semibold text-warning-600">Lotes por vencer (7 días)</h4>
                @forelse($lotes as $lote)
                    <div class="mb-2 rounded-lg border p-2 text-sm">
                        <strong>{{ $lote->producto->nombre }}</strong><br>
                        Lote {{ $lote->codigo_lote }} — vence {{ $lote->fecha_vencimiento->format('d/m/Y') }}<br>
                        Disponible: {{ number_format($lote->cantidad_disponible, 2) }}
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sin lotes por vencer.</p>
                @endforelse
            </div>
            <div>
                <h4 class="mb-2 font-semibold text-danger-600">Stock bajo mínimo</h4>
                @forelse($productosBajoStock as $producto)
                    <div class="mb-2 rounded-lg border p-2 text-sm">
                        <strong>{{ $producto->nombre }}</strong><br>
                        Stock: {{ number_format($producto->stockDisponible(), 2) }} / Mínimo: {{ number_format($producto->stock_minimo, 2) }}
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Todo el stock está sobre el mínimo.</p>
                @endforelse
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
