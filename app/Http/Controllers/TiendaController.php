<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Rol;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TiendaController extends Controller
{
    public function catalogo()
    {
        $productos = Producto::query()
            ->where('activo', true)
            ->where('vendible_online', true)
            ->orderBy('nombre')
            ->get()
            ->map(function (Producto $producto) {
                $producto->stock = $producto->stockDisponible();

                return $producto;
            })
            ->filter(fn (Producto $p) => $p->stock > 0);

        return view('tienda.catalogo', compact('productos'));
    }

    public function carrito()
    {
        return view('tienda.carrito', [
            'items' => $this->itemsCarrito(),
            'total' => $this->totalCarrito(),
        ]);
    }

    public function agregar(Request $request, Producto $producto)
    {
        $cantidad = max(0.001, (float) $request->input('cantidad', 1));

        if ($producto->stockDisponible() < $cantidad) {
            return back()->with('error', 'Stock insuficiente.');
        }

        $carrito = session('carrito', []);
        $id = $producto->id;
        $carrito[$id] = [
            'producto_id' => $id,
            'nombre' => $producto->nombre,
            'precio' => (float) $producto->precio_venta,
            'cantidad' => ($carrito[$id]['cantidad'] ?? 0) + $cantidad,
        ];
        session(['carrito' => $carrito]);

        return redirect()->route('tienda.carrito')->with('success', 'Producto agregado al carrito.');
    }

    public function quitar(Producto $producto)
    {
        $carrito = session('carrito', []);
        unset($carrito[$producto->id]);
        session(['carrito' => $carrito]);

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function checkout()
    {
        if (empty(session('carrito', []))) {
            return redirect()->route('tienda.catalogo')->with('error', 'Tu carrito está vacío.');
        }

        return view('tienda.checkout', [
            'items' => $this->itemsCarrito(),
            'total' => $this->totalCarrito(),
            'cliente' => Auth::user()?->cliente,
        ]);
    }

    public function confirmar(Request $request, VentaService $ventaService)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'notas' => 'nullable|string|max:500',
        ]);

        $items = $this->itemsCarrito();
        if (empty($items)) {
            return redirect()->route('tienda.catalogo')->with('error', 'Tu carrito está vacío.');
        }

        $usuario = Auth::user();
        if (! $usuario) {
            $usuario = User::query()->create([
                'nombre' => $request->nombre,
                'correo' => $request->telefono.'@cliente.kefir.local',
                'contrasena' => Hash::make(str()->random(16)),
                'telefono' => $request->telefono,
                'activo' => true,
            ]);
            $usuario->assignRole(Rol::query()->where('name', 'cliente')->first());
        }

        $cliente = $usuario->cliente ?? Cliente::query()->create([
            'usuario_id' => $usuario->id,
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'tipo_cliente' => 'persona',
            'activo' => true,
        ]);

        $lineas = collect($items)->map(fn ($item) => [
            'producto_id' => $item['producto_id'],
            'cantidad' => $item['cantidad'],
            'precio_unitario' => $item['precio'],
            'descuento' => 0,
        ])->values()->all();

        $venta = $ventaService->crearVenta([
            'cliente_id' => $cliente->id,
            'tipo_venta' => 'pedido_web',
            'canal' => 'web',
            'estado' => 'completada',
            'notas' => $request->notas,
        ], $lineas, $usuario->id);

        session()->forget('carrito');

        return view('tienda.confirmacion', compact('venta'));
    }

    protected function itemsCarrito(): array
    {
        return array_values(session('carrito', []));
    }

    protected function totalCarrito(): float
    {
        return collect($this->itemsCarrito())->sum(fn ($item) => $item['precio'] * $item['cantidad']);
    }
}
