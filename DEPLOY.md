# Despliegue en producción — Kefir System

Guía para publicar el sistema en internet (accesible desde celular u otro dispositivo).

## Opción recomendada: Railway (~5 USD/mes)

Railway conecta tu repo de GitHub y te da una **URL pública HTTPS** automática.

### 1. Crear cuenta

1. Entra a [railway.app](https://railway.app) e inicia sesión con GitHub.
2. Autoriza acceso al repositorio `jhomau/kefir-system`.

### 2. Crear proyecto

1. **New Project** → **Deploy from GitHub repo** → elige `kefir-system`.
2. Railway detectará el `Dockerfile` y comenzará a construir.

### 3. Agregar base de datos

1. En el proyecto, clic en **+ New** → **Database** → **PostgreSQL**.
2. Espera a que esté activa.

### 4. Variables de entorno (servicio web)

En el servicio **kefir-system** (no en la base de datos), pestaña **Variables**:

| Variable | Valor |
|----------|-------|
| `APP_NAME` | `Kefir System` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | *(genera abajo)* |
| `APP_URL` | `https://TU-DOMINIO.up.railway.app` |
| `DB_CONNECTION` | `pgsql` |
| `DATABASE_URL` | `${{Postgres.DATABASE_URL}}` |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `LOG_CHANNEL` | `stderr` |

**Generar APP_KEY** (en tu PC, dentro del proyecto):

```bash
php artisan key:generate --show
```

Copia el valor `base64:...` y pégalo en `APP_KEY`.

> Para `DATABASE_URL`: usa el botón **Add Reference** y selecciona la variable `DATABASE_URL` del servicio PostgreSQL.

### 5. Dominio público

1. Servicio web → **Settings** → **Networking** → **Generate Domain**.
2. Copia la URL (ej. `https://kefir-system-production.up.railway.app`).
3. Actualiza `APP_URL` con esa URL exacta y redeploy si hace falta.

### 6. Probar desde el celular

Abre en el navegador del celular:

```
https://TU-DOMINIO.up.railway.app/admin
```

| Campo | Valor |
|-------|-------|
| Correo | `admin@kefir.local` |
| Contraseña | `password` |

> Cambia la contraseña del admin después del primer acceso en producción.

---

## Opción alternativa: Render

1. [render.com](https://render.com) → **New** → **Blueprint** → conecta el repo.
2. Render leerá `render.yaml` (requiere plan de pago para web + PostgreSQL).
3. Configura `APP_URL` con la URL que te asigne Render.

---

## Actualizar producción

Cada `git push` a `main` en GitHub puede redeployar automáticamente si activaste auto-deploy en Railway/Render.

```bash
git add .
git commit -m "Tu cambio"
git push origin main
```

---

## Costo estimado

| Servicio | Costo aprox. |
|----------|----------------|
| Railway (web + PostgreSQL) | ~5–10 USD/mes |
| Render (starter) | ~7–14 USD/mes |

Para una demo corta, Railway suele incluir crédito inicial gratis.
