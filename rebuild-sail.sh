#!/bin/bash
# ==============================================
# 🚀 REBUILD SAIL ENVIRONMENT - BgaGO Project
# ==============================================
# Autor: Paula / Omni-Boy
# Descripción: Reinicia y reconstruye por completo
# el entorno Docker de Laravel Sail, con chequeos
# automáticos de MySQL, migraciones y cache.
# ==============================================

echo ""
echo "🧹 Limpiando contenedores previos..."
./vendor/bin/sail down -v

echo ""
echo "🧼 Limpiando cachés de Docker..."
docker builder prune -af

echo ""
echo "🏗️ Reconstruyendo entorno (sin caché)..."
./vendor/bin/sail build --no-cache

echo ""
echo "🚀 Iniciando contenedores en segundo plano..."
./vendor/bin/sail up -d

echo ""
echo "⏳ Esperando a que MySQL se inicie..."
sleep 10
./vendor/bin/sail ps

echo ""
echo "🔑 Generando clave de aplicación (por si falta)..."
./vendor/bin/sail artisan key:generate

echo ""
echo "🧩 Limpiando cachés de Laravel..."
./vendor/bin/sail artisan optimize:clear

echo ""
echo "📦 Ejecutando migraciones..."
./vendor/bin/sail artisan migrate --force

echo ""
echo "📡 Verificando servicios activos..."
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo ""
echo "✅ Entorno listo en http://localhost"
echo "----------------------------------------------"
echo "✔ Laravel        : corriendo en puerto 80"
echo "✔ MySQL          : puerto 3306 interno / 3307 host"
echo "✔ Mosquitto MQTT : puerto 1883"
echo "✔ Publisher/Sub. : conectados en red 'sail'"
echo "----------------------------------------------"
echo ""
echo "📜 Últimos logs de Laravel:"
./vendor/bin/sail logs laravel.test --tail=20
