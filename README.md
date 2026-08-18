# kefir-system

Sistema de información para control de inventarios, ventas y clientes — producto: **kefir de leche**.

## Stack

- Laravel 11
- Filament 3 (panel admin)
- Spatie Laravel Permission (roles dinámicos)
- SQLite (local) / PostgreSQL (producción)

## Instalación local

```bash
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
serve-artisan.bat
```

## Accesos

| Área | URL |
|------|-----|
| Admin | http://127.0.0.1:8000/admin |
| Tienda web | http://127.0.0.1:8000/tienda |

**Admin:** `admin@kefir.local` / `password`

## Módulos implementados

### Fase 1 — Base
- Usuarios, roles, permisos
- Clientes y productos

### Fase 2 — Inventario
- Lotes de producción (`InventarioService`)
- Movimientos (producción, venta, ajuste, merma, devolución)
- Stock por producto y alertas de vencimiento/stock bajo

### Fase 3 — Ventas
- Registro de ventas con detalle (`VentaService`)
- Pagos parciales/totales
- Anulación con devolución de stock (FIFO por lote)

### Fase 4 — Finanzas
- Gastos, categorías, otros ingresos
- Dashboard con estadísticas del mes
- Página de reportes

### Fase 5 — Tienda web
- Catálogo móvil en `/tienda`
- Carrito y pedidos web (`pedido_web` / canal `web`)

## Flujo recomendado de prueba

1. Crear productos (o usar demo seeder)
2. **Inventario → Lotes** — registrar producción
3. **Ventas → Ventas** — registrar venta y pagos
4. **Finanzas** — registrar gastos y ver reportes
5. Marcar producto como **vendible en tienda web** y probar `/tienda`

## Roles

| Rol | Acceso |
|-----|--------|
| administrador | Todo |
| vendedor | Clientes, ventas, pagos, inventario (lectura), reportes |
| cliente | Tienda web (pedidos) |

## Producción

Ver [DEPLOY.md](DEPLOY.md) para Railway/Render.
