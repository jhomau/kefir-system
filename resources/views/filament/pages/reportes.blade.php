<x-filament-panels::page>
    <div class="grid gap-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <x-filament::section>
                <p class="text-sm text-gray-500">Ventas ({{ $periodo }})</p>
                <p class="text-2xl font-bold text-success-600">Bs. {{ number_format($totalVentas, 2) }}</p>
                <p class="text-xs text-gray-400">{{ $cantidadVentas }} ventas</p>
            </x-filament::section>
            <x-filament::section>
                <p class="text-sm text-gray-500">Gastos</p>
                <p class="text-2xl font-bold text-danger-600">Bs. {{ number_format($totalGastos, 2) }}</p>
            </x-filament::section>
            <x-filament::section>
                <p class="text-sm text-gray-500">Otros ingresos</p>
                <p class="text-2xl font-bold text-primary-600">Bs. {{ number_format($totalIngresos, 2) }}</p>
            </x-filament::section>
            <x-filament::section>
                <p class="text-sm text-gray-500">Utilidad estimada</p>
                <p class="text-2xl font-bold">Bs. {{ number_format($utilidad, 2) }}</p>
            </x-filament::section>
        </div>

        <x-filament::section heading="Gastos por categoría">
            <div class="space-y-2">
                @forelse($gastosPorCategoria as $categoria => $monto)
                    <div class="flex justify-between border-b pb-2">
                        <span>{{ $categoria }}</span>
                        <span class="font-medium">Bs. {{ number_format($monto, 2) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500">Sin gastos este mes.</p>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section heading="Ventas recientes del mes">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2">Número</th>
                            <th class="py-2">Cliente</th>
                            <th class="py-2">Total</th>
                            <th class="py-2">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventasRecientes as $venta)
                            <tr class="border-b">
                                <td class="py-2">{{ $venta->numero_venta }}</td>
                                <td class="py-2">{{ $venta->cliente?->nombre ?? '—' }}</td>
                                <td class="py-2">Bs. {{ number_format($venta->total, 2) }}</td>
                                <td class="py-2">{{ $venta->fecha_venta?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
