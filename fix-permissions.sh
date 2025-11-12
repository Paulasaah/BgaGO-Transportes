#!/bin/bash
# ==========================================================
# 🔧 Fix Laravel Sail Permissions & Cache
# ==========================================================
# Compatible con entornos Docker Sail (UID=1000 en host)
# ==========================================================

echo "🔧 Corrigiendo permisos y entorno de Laravel..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# ----------------------------------------------------------
# Verificar si se ejecuta dentro del contenedor
# ----------------------------------------------------------
check_docker() {
    if [ -f /.dockerenv ]; then
        echo -e "${GREEN}✓${NC} Ejecutando dentro de Docker"
        return 0
    else
        echo -e "${YELLOW}⚠${NC} No estás en Docker, usa: sail bash"
        return 1
    fi
}

# ----------------------------------------------------------
# Asegurar estructura básica (sin cambiar ownership)
# ----------------------------------------------------------
fix_storage_structure() {
    echo -e "${YELLOW}→${NC} Verificando estructura de directorios..."

    mkdir -p /var/www/html/storage/logs
    mkdir -p /var/www/html/storage/framework/{cache/data,sessions,views}
    mkdir -p /var/www/html/storage/app/public

    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

    echo -e "${GREEN}✓${NC} Directorios verificados correctamente"
}

# ----------------------------------------------------------
# Limpiar cachés de Laravel
# ----------------------------------------------------------
clear_cache() {
    echo -e "${YELLOW}→${NC} Limpiando caché de Laravel..."

    cd /var/www/html || exit 1

    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    php artisan optimize:clear
    php artisan permission:cache-reset 2>/dev/null || true

    echo -e "${GREEN}✓${NC} Caché limpiado correctamente"
}

# ----------------------------------------------------------
# Crear base de datos de testing
# ----------------------------------------------------------
create_testing_db() {
    echo -e "${YELLOW}→${NC} Verificando base de datos de testing..."

    mysql -h mysql -u root -psecret -e "CREATE DATABASE IF NOT EXISTS testing;" 2>/dev/null
    mysql -h mysql -u root -psecret -e "GRANT ALL PRIVILEGES ON testing.* TO 'sail'@'%';" 2>/dev/null
    mysql -h mysql -u root -psecret -e "FLUSH PRIVILEGES;" 2>/dev/null

    if mysql -h mysql -u sail -psecret -e "USE testing;" 2>/dev/null; then
        echo -e "${GREEN}✓${NC} Base de datos 'testing' lista"
    else
        echo -e "${RED}✗${NC} No se pudo crear/verificar la base de datos 'testing'"
    fi
}

# ----------------------------------------------------------
# Verificar archivos críticos (.env, phpunit.xml)
# ----------------------------------------------------------
check_files() {
    echo -e "${YELLOW}→${NC} Verificando archivos críticos..."

    if [ -f "/var/www/html/.env" ]; then
        echo -e "${GREEN}✓${NC} Archivo .env existe"
    elif [ -f "/var/www/html/.env.example" ]; then
        echo -e "${YELLOW}→${NC} Copiando .env.example a .env..."
        cp /var/www/html/.env.example /var/www/html/.env
    fi

    if [ -f "/var/www/html/phpunit.xml" ]; then
        echo -e "${GREEN}✓${NC} phpunit.xml existe"
    elif [ -f "/var/www/html/phpunit.xml.dist" ]; then
        echo -e "${YELLOW}→${NC} Copiando phpunit.xml.dist a phpunit.xml..."
        cp /var/www/html/phpunit.xml.dist /var/www/html/phpunit.xml
    fi
}

# ----------------------------------------------------------
# Ejecución principal
# ----------------------------------------------------------
main() {
    echo ""
    echo "╔══════════════════════════════════════════╗"
    echo "║   🔧 Fix Laravel Sail Permissions        ║"
    echo "╚══════════════════════════════════════════╝"
    echo ""

    # check_docker || exit 1

    fix_storage_structure
    check_files
    create_testing_db
    clear_cache

    echo ""
    echo -e "${GREEN}✓ CORRECCIONES COMPLETADAS${NC}"
    echo ""
    echo "Ahora puedes ejecutar:"
    echo "  → php artisan test"
    echo "  → php artisan serve --host=0.0.0.0 --port=80"
    echo ""
}

main
