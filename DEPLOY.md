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
| `APP_URL` | *(opcional: Railway la detecta sola con `RAILWAY_PUBLIC_DOMAIN`)* |
| `DB_CONNECTION` | `pgsql` |
| `DATABASE_URL` | `${{Postgres.DATABASE_URL}}` |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER` | `file` |
| `CACHE_STORE` | `file` |
| `LOG_CHANNEL` | `stderr` |

> **Importante:** Si tenías `APP_URL=http://localhost`, eso causa error 500 en produccion. Borra esa variable o pon la URL real de Railway.

**Generar APP_KEY** (en tu PC, dentro del proyecto):

```bash
php artisan key:generate --show
```

Copia el valor `base64:...` y pégalo en `APP_KEY`.

> Para `DATABASE_URL`: usa el botón **Add Reference** → selecciona el servicio **PostgreSQL** → variable `DATABASE_URL`.

> **No agregues** `DB_HOST=127.0.0.1` ni `DB_PORT=5432` manualmente — eso causa el error "Connection refused".

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

---

## Error: Connection refused 127.0.0.1:5432

Significa que **PostgreSQL no está conectado** al servicio web.

1. En Railway, verifica que exista un servicio **PostgreSQL** en el mismo proyecto.
2. En el servicio **web** (kefir-system) → **Variables**:
   - Clic **Add Reference** → PostgreSQL → `DATABASE_URL`
3. **Elimina** variables manuales: `DB_HOST`, `DB_PORT`, `DB_DATABASE` si las creaste.
4. Redeploy.

---

## Error 500 en producción

Si ves **Server Error 500**:

1. Abre `https://TU-URL.up.railway.app/health` — si falla, el problema es la **base de datos**.
2. Verifica en Railway: `APP_KEY`, `DATABASE_URL`, `APP_URL`, `DB_SSLMODE=require`.
3. Cambia temporalmente `APP_DEBUG=true`, redeploy, mira el error en pantalla y vuelve a `false`.
4. En **Deployments → View logs**, busca líneas rojas de PHP/Laravel.
5. Asegúrate de usar el último deploy en `main`.
