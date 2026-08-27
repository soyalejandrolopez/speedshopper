#!/usr/bin/env bash
#
# Deploy script for SpeedShopper on cPanel shared hosting.
# Run from the project root, e.g.:  bash deploy.sh
#   or:  cd ~/public_html/website_54d8238e/core && bash deploy.sh
#
set -e

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

echo "==> Deploying SpeedShopper in $APP_DIR"

# 1) Permisos de escritura
echo "==> Permisos"
chmod -R 775 storage bootstrap/cache || true
find storage -type d -exec chmod 775 {} + || true
find storage -type f -exec chmod 664 {} + || true

# 2) Carpetas públicas
echo "==> Carpetas públicas"
mkdir -p storage/app/public/packages storage/app/public/branding

# 3) Carpeta pública de uploads (real, sin symlinks — evita el 403 en cPanel/Bluehost)
echo "==> Carpeta pública de uploads"
if [ -L public/storage ]; then
    rm -f public/storage
fi
mkdir -p public/storage/packages public/storage/branding
chmod -R 775 public/storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 4) Dependencias
echo "==> Composer"
if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
elif [ -f "$HOME/composer.phar" ]; then
    php "$HOME/composer.phar" install --no-dev --optimize-autoloader --no-interaction --prefer-dist
else
    echo "!! composer no encontrado; instala las dependencias manualmente"
fi

# 5) Artisan (solo si existe .env)
if [ -f .env ]; then
    echo "==> Artisan (limpiar caché + migrar + recaché)"
    php artisan optimize:clear || true
    php artisan migrate --force || true
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
else
    echo "!! .env no existe: crea .env y ejecuta:"
    echo "    php artisan key:generate"
    echo "    php artisan migrate --force"
fi

echo "==> Deploy listo"
