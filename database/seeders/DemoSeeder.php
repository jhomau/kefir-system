<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'codigo_producto' => 'KEF-500',
                'nombre' => 'Kefir natural 500 ml',
                'descripcion' => 'Kefir de leche natural, botella de 500 ml.',
                'unidad_medida' => 'botella',
                'precio_venta' => 12.00,
                'precio_costo' => 6.50,
                'stock_minimo' => 10,
                'vendible_online' => true,
            ],
            [
                'codigo_producto' => 'KEF-1L',
                'nombre' => 'Kefir natural 1 litro',
                'descripcion' => 'Kefir de leche natural, envase de 1 litro.',
                'unidad_medida' => 'litro',
                'precio_venta' => 22.00,
                'precio_costo' => 11.00,
                'stock_minimo' => 8,
                'vendible_online' => true,
            ],
            [
                'codigo_producto' => 'KEF-FRA',
                'nombre' => 'Kefir con frutas 500 ml',
                'descripcion' => 'Kefir sabor frutas, botella de 500 ml.',
                'unidad_medida' => 'botella',
                'precio_venta' => 14.00,
                'precio_costo' => 7.00,
                'stock_minimo' => 6,
                'vendible_online' => true,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::query()->firstOrCreate(
                ['codigo_producto' => $producto['codigo_producto']],
                array_merge($producto, ['activo' => true])
            );
        }
    }
}
