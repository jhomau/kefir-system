<?php

namespace Database\Seeders;

use App\Models\CategoriaGasto;
use Illuminate\Database\Seeder;

class CategoriasGastoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Leche e insumos',
            'Envases y empaques',
            'Transporte',
            'Servicios',
            'Otros',
        ];

        foreach ($categorias as $nombre) {
            CategoriaGasto::query()->firstOrCreate(
                ['nombre' => $nombre],
                ['activo' => true]
            );
        }
    }
}
