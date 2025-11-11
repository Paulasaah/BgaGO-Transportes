#!/bin/bash
# ==========================================================
# 🔍 Test Script API BgaGO - SEGURO Y OPTIMIZADO
# ==========================================================
set -u -o pipefail

# Detección de entorno
if [ -f /.dockerenv ]; then
  URL="http://laravel.test"
else
  URL="http://127.0.0.1:8000"
fi

TOKENS_FILE="storage/test-tokens.txt"

# Verificación del archivo de tokens
if [ ! -f "$TOKENS_FILE" ]; then
  echo "⚠️  Archivo de tokens no encontrado en: $TOKENS_FILE"
  echo "Ejecuta el script de setup en Tinker para generarlos."
  exit 1
fi

# Leer tokens
ADMIN_TOKEN=$(grep -E "^ADMIN:" "$TOKENS_FILE" | awk '{print $2}')
CLIENTE_TOKEN=$(grep -E "^CLIENTE:" "$TOKENS_FILE" | awk '{print $2}')
CONDUCTOR_TOKEN=$(grep -E "^CONDUCTOR:" "$TOKENS_FILE" | awk '{print $2}')

if [ -z "${ADMIN_TOKEN:-}" ] || [ -z "${CLIENTE_TOKEN:-}" ] || [ -z "${CONDUCTOR_TOKEN:-}" ]; then
  echo "❌ Error: no se pudieron leer los tokens correctamente."
  exit 1
fi

# Colores
GREEN="\e[32m"; RED="\e[31m"; BLUE="\e[34m"; YELLOW="\e[33m"; GRAY="\e[90m"; RESET="\e[0m"

separator() { echo -e "\n${GRAY}-------------------------------------------------------${RESET}\n"; }

# Función de prueba segura
test_request() {
  local desc=$1
  local cmd=$2
  echo -e "${BLUE}▶️  $desc${RESET}"

  response=$(bash -c "$cmd -s -w '\n%{http_code}'" 2>/dev/null)
  body=$(echo "$response" | head -n -1)
  code=$(echo "$response" | tail -n 1)
  message=$(echo "$body" | jq -r '.message // .error // .detail // .status // empty')

  if [[ "$code" =~ ^2 ]]; then
    echo -e "  ${GREEN}✔️  OK ($code)${RESET} ${GRAY}${message}${RESET}"
  elif [[ "$code" == "403" ]]; then
    echo -e "  ${RED}❌  FORBIDDEN ($code)${RESET} ${GRAY}${message}${RESET}"
  elif [[ "$code" == "401" ]]; then
    echo -e "  ${RED}❌  UNAUTHORIZED ($code)${RESET} ${GRAY}${message}${RESET}"
  else
    echo -e "  ${RED}⚠️  ERROR ($code)${RESET} ${GRAY}${message}${RESET}"
  fi

  if [ "${VERBOSE:-0}" == "1" ]; then
    echo -e "${YELLOW}--- Respuesta completa ---${RESET}"
    echo "$body" | jq
  fi
}

# Inicio
echo -e "\n🚀 ${GREEN}INICIANDO TESTS AUTOMÁTICOS DE LA API BgaGO${RESET}\n"
separator

# ==========================================================
# 👤 ADMIN
# ==========================================================
echo -e "${BLUE}👤 TESTS ADMIN${RESET}"
test_request "Ver todas las reservas" \
"curl -X GET $URL/api/reservations -H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN'"
test_request "Ver una reserva específica" \
"curl -X GET $URL/api/reservations/1 -H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN'"
test_request "Cancelar reserva" \
"curl -X POST $URL/api/reservations/1/cancel \
-H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN' \
-H 'Content-Type: application/json' -d '{\"motivo\":\"Usuario no se presentó a la hora acordada\"}'"
test_request "Ver todos los pagos" \
"curl -X GET $URL/api/payments -H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN'"
test_request "Aprobar un pago" \
"curl -X POST $URL/api/payments/1/approve \
-H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN' \
-H 'Content-Type: application/json' -d '{\"referencia_externa\": \"REF-123456\"}'"
separator

# ==========================================================
# 👩‍💻 CLIENTE
# ==========================================================
echo -e "${BLUE}👩‍💻 TESTS CLIENTE${RESET}"
test_request "Intentar ver todas las reservas (403 esperado)" \
"curl -X GET $URL/api/reservations -H 'Accept: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN'"
test_request "Ver sus propias reservas" \
"curl -X GET $URL/api/reservations/me/list -H 'Accept: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN'"
test_request "Crear nueva reserva" \
"curl -X POST $URL/api/reservations \
-H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' \
-d '{\"vehiculo_id\":1,\"sede_id\":1,\"origen_direccion\":\"Sede Cabecera\",\"destino_direccion\":\"Sede Cabecera\",\
\"fecha_inicio\":\"2025-11-15 10:00:00\",\"fecha_fin\":\"2025-11-15 18:00:00\"}'"
test_request "Ver catálogo de vehículos" \
"curl -X GET $URL/api/vehicles -H 'Accept: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN'"
test_request "Verificar disponibilidad de vehículo" \
"curl -X POST $URL/api/vehicles/1/check-availability \
-H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' \
-d '{\"fecha_inicio\":\"2025-11-16 09:00:00\",\"fecha_fin\":\"2025-11-16 17:00:00\"}'"
test_request "Crear domicilio de paquete" \
"curl -X POST $URL/api/deliveries/package \
-H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' \
-d '{\"origen_direccion\":\"Carrera 33 #42-123, Bucaramanga\",\"origen_lat\":7.119349,\"origen_lon\":-73.122742,\
\"destino_direccion\":\"Calle 56 #23-45, Floridablanca\",\"destino_lat\":7.065394,\"destino_lon\":-73.086609,\
\"descripcion\":\"Documentos importantes\",\"peso_kg\":2,\"requiere_firma\":true,\
\"contacto_nombre\":\"Juan Pérez\",\"contacto_telefono\":\"3001234567\"}'"
test_request "Ver mis domicilios" \
"curl -X GET $URL/api/deliveries/me/list -H 'Accept: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN'"
test_request "Intentar asignar conductor (403 esperado)" \
"curl -X POST $URL/api/deliveries/1/assign-driver \
-H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' \
-d '{\"conductor_id\":3}'"
separator

# ==========================================================
# 🚗 CONDUCTOR
# ==========================================================
echo -e "${BLUE}🚗 TESTS CONDUCTOR${RESET}"
test_request "Ver entregas asignadas a mí" \
"curl -X GET $URL/api/deliveries/me/assigned -H 'Accept: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN'"
test_request "Iniciar entrega asignada" \
"curl -X POST $URL/api/deliveries/1/start -H 'Accept: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN'"
test_request "Completar entrega" \
"curl -X POST $URL/api/deliveries/1/complete \
-H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN' \
-d '{\"firma_recibido\":\"Juan Pérez\",\"comentario\":\"Entrega exitosa\"}'"
test_request "Tracking de entrega" \
"curl -X GET $URL/api/deliveries/1/track -H 'Accept: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN'"
separator

# ==========================================================
# 🌐 PÚBLICOS
# ==========================================================
echo -e "${BLUE}🌐 TESTS PÚBLICOS (sin token)${RESET}"
test_request "Estado del API" "curl -X GET $URL/api/status -H 'Accept: application/json'"
test_request "Listar vehículos (público)" "curl -X GET $URL/api/vehicles -H 'Accept: application/json'"
test_request "Ver detalle de vehículo" "curl -X GET $URL/api/vehicles/1 -H 'Accept: application/json'"
test_request "Listar sedes" "curl -X GET $URL/api/branches -H 'Accept: application/json'"
test_request "Ver una sede" "curl -X GET $URL/api/branches/1 -H 'Accept: application/json'"
test_request "Buscar sede más cercana" \
"curl -X POST $URL/api/branches/nearest -H 'Accept: application/json' -H 'Content-Type: application/json' \
-d '{\"lat\":7.119349,\"lon\":-73.122742}'"
test_request "Métodos de pago disponibles" "curl -X GET $URL/api/payments/methods/available -H 'Accept: application/json'"
test_request "Estadísticas generales (telemetría)" "curl -X GET $URL/api/estadisticas -H 'Accept: application/json'"
test_request "Últimas posiciones GPS" "curl -X GET $URL/api/telemetria/latest -H 'Accept: application/json'"
separator
echo -e "✅ ${GREEN}PRUEBAS COMPLETADAS${RESET}"
echo -e "${GRAY}Archivo tokens usado: $TOKENS_FILE${RESET}\n"
