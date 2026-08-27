# Despliegue — GitHub y cPanel

Guía para subir el proyecto a GitHub y publicarlo en un hosting compartido con **cPanel** (Apache + PHP 8.3+).

---

## 1) Subir a GitHub

El repositorio ya está inicializado y con el commit inicial. Solo necesitas crear el repositorio en GitHub y vincularlo:

```bash
# Crea un repo vacío en GitHub (sin README) y luego:
git remote add origin https://github.com/TU_USUARIO/speedshopper.git
git branch -M main
git push -u origin main
```

> El archivo `.env`, `vendor/`, `node_modules/`, `public/build/` y `storage/*.key` **no** se suben (están en `.gitignore`).

---

## 2) Desplegar en cPanel

### Requisitos del hosting
- PHP **8.3 o superior** con extensiones: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `zip`
- MySQL (crear una base de datos y un usuario en cPanel → "MySQL Databases")

### Opción A — Subir por File Manager
1. Comprime el proyecto (excluyendo `vendor/`, `node_modules/`, `.env`).
2. En cPanel → **File Manager** → entra a `public_html` → **Subir** → descomprime.
3. (Opcional pero recomendado) Pon el documento raíz en la carpeta `public`:
   - cPanel → **Domains** → edita tu dominio → "Document Root" → apunta a `public_html/public`.
   - Si no puedes cambiarlo, el `.htaccess` raíz ya redirige todo a `public/`.

### Opción B — Desplegar con Git (si cPanel trae Git)
- cPanel → **Git Version Control** → crear repositorio con tu URL de GitHub.
- En "Copy to .cpanel" elige `public_html`.

### Configurar la aplicación
1. **Crear el `.env`** (copia de `.env.example`):
   ```bash
   cp .env.example .env
   ```
   Ajusta al menos:
   ```ini
   APP_NAME=SpeedShopper
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://tudominio.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=cpanel_db_name
   DB_USERNAME=cpanel_db_user
   DB_PASSWORD=tu_password

   MAIL_MAILER=smtp
   MAIL_HOST=smtp.tudominio.com
   MAIL_PORT=587
   MAIL_USERNAME=...
   MAIL_PASSWORD=...
   MAIL_FROM_ADDRESS=no-reply@tudominio.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

2. **Instalar dependencias** (por Terminal/SSH de cPanel, o sube `vendor/`):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Generar la llave**:
   ```bash
   php artisan key:generate
   ```

4. **Permisos** (storage debe ser escribible):
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

5. **Migrar la base de datos**:
   ```bash
   php artisan migrate --force
   ```

6. **Enlazar el storage público** (fotos, logo, favicon):
   ```bash
   php artisan storage:link
   ```
   Si no hay terminal, crea el enlace manualmente: dentro de `public/` un enlace simbólico `storage -> ../storage/app/public`.

7. **Compilar assets** (si el hosting tiene Node; si no, sube la carpeta `public/build`):
   ```bash
   npm ci
   npm run build
   ```

8. **Optimizar producción**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

9. **Configurar desde el admin** (panel):
   - Entra como admin (`admin@speedshopper.com` / `password`) y cambia la contraseña.
   - Configura tu **SMTP**, **logo/favicon**, **color del sitio**, WhatsApp/API y fees en **Configuración**.

### Notas
- `php artisan storage:link` / `config:cache` requieren terminal; en muchos planes cPanel está disponible en **Terminal** o por **SSH**.
- Si la app marca 500 tras desplegar, revisa `storage/logs/laravel.log` y los permisos de `storage/`.
- El login usa correo; la verificación de email requiere SMTP configurado.

---

## Despliegue rápido en `~/public_html/website_54d8238e`

Tu sitio estará en **`public_html/website_54d8238e`**. Sigue esto:

1. **Sube `speedshopper-cpanel.zip`** a `public_html/website_54d8238e` (File Manager → Subir) y descomprímelo ahí (que los archivos queden directamente en esa carpeta, sin una carpeta intermedia).

2. **Pon el Document Root en la carpeta `public`** (recomendado):
   - cPanel → **Domains** → tu dominio → editar → **Document Root** → `public_html/website_54d8238e/public`
   - Si no puedes cambiarlo, el `.htaccess` raíz ya redirige a `public/`.

3. **Terminal/SSH** dentro de `~/public_html/website_54d8238e`:
   ```bash
   # Permisos
   find storage bootstrap/cache -type d -exec chmod 775 {} +
   chmod 664 storage/framework/views/*.php storage/framework/sessions/* 2>/dev/null

   # Dependencias (si el hosting trae Composer)
   composer install --no-dev --optimize-autoloader

   # Configuración
   cp .env.example .env
   #  → edita .env: APP_ENV=production, APP_DEBUG=false, APP_URL=https://tudominio.com,
   #    DB_* (MySQL de cPanel), MAIL_* (SMTP)

   # Llave, migraciones, storage y build
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   npm ci && npm run build        # si hay Node; si no, sube la carpeta public/build

   # Optimización
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. Si tu plan **no tiene Terminal ni Composer**, dime y preparo un ZIP con `vendor/` y `public/build` incluidos para que la app corra sin terminal.
