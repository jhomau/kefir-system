# kefir-system

Sistema de información para control de inventarios, ventas y clientes — producto: **kefir de leche**.

## Stack

- Laravel 11
- Spatie Laravel Permission (roles dinámicos)
- MySQL / SQLite
- Filament 3 (próximo paso)

## Requisitos

- PHP 8.2+
- Composer
- MySQL (XAMPP) o SQLite

## Instalación

```bash
# Usar PHP 8.2+ (no el de XAMPP 8.0)
copy .env.example .env
php artisan key:generate

# MySQL en XAMPP (.env):
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kefir_system
# DB_USERNAME=root
# DB_PASSWORD=

php artisan migrate --seed
php artisan serve
```

## Usuario admin inicial

| Campo | Valor |
|-------|-------|
| Correo | admin@kefir.local |
| Contraseña | password |

## Tablas creadas (17)

**Seguridad:** usuarios, roles, permisos, rol_permisos, usuario_roles, usuario_permisos

**Negocio:** clientes, productos, lotes_producto, movimientos_inventario, ventas, detalle_ventas, pagos

**Finanzas:** categorias_gasto, gastos, otros_ingresos

## Roles iniciales

- **administrador** — todos los permisos
- **vendedor** — clientes, ventas, pagos, inventario (lectura)
- **cliente** — catálogo web y reservas (fase 3)
