#!/bin/bash
# ===========================================
# 🧰 Laravel Fix Permissions for Docker Sail
# Contenedor: bgago-laravel.test-1
# Autor: Miguel Zaech (Omni-Boy)
# ===========================================

echo "🔧 Corrigiendo permisos en contenedor Laravel..."

APP_PATH="/var/www/html"

# Carpetas críticas
chown -R sail:sail $APP_PATH/storage $APP_PATH/bootstrap/cache
chmod -R 775 $APP_PATH/storage $APP_PATH/bootstrap/cache

# Logs (si no existen)
mkdir -p $APP_PATH/storage/logs
touch $APP_PATH/storage/logs/laravel.log
chown sail:sail $APP_PATH/storage/logs/laravel.log
chmod 775 $APP_PATH/storage/logs/laravel.log

echo "✅ Permisos corregidos correctamente."
