#!/bin/bash
set -e

# Limpiar cache de configuración vieja y recachear con las variables de Railway
php artisan config:clear
php artisan config:cache
php artisan route:cache || echo "Warning: route:cache failed, continuing without route cache"
php artisan view:cache

# Ejecutar migraciones
php artisan migrate --force

# Iniciar el servidor
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
