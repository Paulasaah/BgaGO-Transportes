#!/bin/bash
# ==========================================================
# 🚀 Test Script API BgaGO - VERSIÓN 3.3 (Corregido)
# ==========================================================
set -u
trap 'echo -e "\n❌ ${RED}Error inesperado en la línea $LINENO${RESET}"; exit 1' ERR

# ==========================================================
# 🔍 Detección de entorno (Docker o local)
# ==========================================================
if [ -f /.dockerenv ]; then
  URL="http://laravel.test"
else
  URL="http://127.0.0.1:8000"
fi

TOKENS_FILE="storage/test-tokens.txt"

# ==========================================================
# 🧾 Verificación del archivo de tokens
# ==========================================================
if [ ! -f "$TOKENS_FILE" ]; then
  echo "⚠️  Archivo de tokens no encontrado en: $TOKENS_FILE"
  echo "Ejecuta el script de setup en Tinker para generarlos."
  exit 1
fi

# ==========================================================
# 🔑 Leer tokens
# ==========================================================
ADMIN_TOKEN=$(grep -E "^ADMIN:" "$TOKENS_FILE" | awk '{print $2}')
CLIENTE_TOKEN=$(grep -E "^CLIENTE:" "$TOKENS_FILE" | awk '{print $2}')
CONDUCTOR_TOKEN=$(grep -E "^CONDUCTOR:" "$TOKENS_FILE" | awk '{print $2}')

if [ -z "${ADMIN_TOKEN:-}" ] || [ -z "${CLIENTE_TOKEN:-}" ] || [ -z "${CONDUCTOR_TOKEN:-}" ]; then
  echo "❌ Error: no se pudieron leer los tokens correctamente."
  exit 1
fi

# ==========================================================
# 🎨 Colores y helpers
# ==========================================================
GREEN="\e[32m"; RED="\e[31m"; BLUE="\e[34m"; YELLOW="\e[33m"; GRAY="\e[90m"; RESET="\e[0m"
separator() { echo -e "\n${GRAY}-------------------------------------------------------${RESET}\n"; }

# ==========================================================
# 📋 Logging automático
# ==========================================================
LOG_FILE="storage/logs/test_api_$(date +%F_%H-%M-%S).log"
mkdir -p storage/logs
exec > >(tee -a "$LOG_FILE") 2>&1

# ==========================================================
# 🧠 Función de prueba segura
# ==========================================================
test_request() {
  local desc=$1
  local cmd=$2
  echo -e "${BLUE}▶️  $desc${RESET}"

  response=$(bash -c "$cmd -s -m 15 -w '\n%{http_code}'" 2>/dev/null)
  body=$(echo "$response" | head -n -1)
  code=$(echo "$response" | tail -n 1)
  message=$(echo "$body" | jq -r '.message // .error // .detail // .status // empty' 2>/dev/null)

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
    echo "$body" | jq 2>/dev/null || echo "$body"
  fi
}

# ==========================================================
# 🚀 INICIO
# ==========================================================
echo -e "\n🚀 ${GREEN}INICIANDO TESTS AUTOMÁTICOS DE LA API BgaGO${RESET}\n"
separator

# ==========================================================
# 👤 ADMIN
# ==========================================================
echo -e "${BLUE}👤 TESTS ADMIN${RESET}"

test_request "Ver todas las reservas" \
"curl -L -X GET $URL/api/reservations -H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN'"

test_request "Ver una reserva específica" \
"curl -L -X GET $URL/api/reservations/1 -H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN'"

test_request "Cancelar reserva" \
"curl -L -X POST $URL/api/reservations/1/cancel \
-H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN' \
-H 'Content-Type: application/json' -d '{\"motivo_cancelacion\":\"Usuario no se presentó a la hora acordada\"}'"

test_request "Ver todos los pagos" \
"curl -L -X GET $URL/api/payments -H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN'"

# Buscar dinámicamente un pago pendiente (soporta .estado o .status)
set +e
PENDING_PAYMENT_ID=$(curl -s -H "Accept: application/json" -H "Authorization: Bearer $ADMIN_TOKEN" \
"$URL/api/payments" | jq -r '
  (.. | objects | select(has("estado") or has("status")) 
   | select(.estado=="pendiente" or .status=="pending") | .id) // empty
' 2>/dev/null | head -n 1)
set -e

if [ -z "$PENDING_PAYMENT_ID" ] || [ "$PENDING_PAYMENT_ID" = "null" ]; then
  echo -e "${YELLOW}⚠️  No hay pagos pendientes para aprobar.${RESET}"
else
  test_request "Aprobar un pago pendiente" \
  "curl -L -X POST $URL/api/payments/$PENDING_PAYMENT_ID/approve \
  -H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN' \
  -H 'Content-Type: application/json' -d '{\"referencia_externa\": \"REF-123456\"}'"
fi

separator

# ==========================================================
# 👩‍💻 CLIENTE
# ==========================================================
echo -e "${BLUE}👩‍💻 TESTS CLIENTE${RESET}"

test_request "Intentar ver todas las reservas (403 esperado)" \
"curl -L -X GET $URL/api/reservations -H 'Accept: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN'"

test_request "Ver sus propias reservas" \
"curl -L -X GET $URL/api/reservations/me/list -H 'Accept: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN'"

# Buscar vehículo disponible
set +e
AVAILABLE_VEHICLE_ID=$(curl -s "$URL/api/vehicles" -H "Accept: application/json" -H "Authorization: Bearer $CLIENTE_TOKEN" | jq -r '
  (.. | objects | select(has("estado")) | select(.estado=="disponible") | .id) // empty
' 2>/dev/null | head -n 1)
set -e

if [ -z "$AVAILABLE_VEHICLE_ID" ] || [ "$AVAILABLE_VEHICLE_ID" = "null" ]; then
  echo -e "${YELLOW}⚠️  No hay vehículos disponibles.${RESET}"
else
  test_request "Crear nueva reserva (vehículo disponible)" \
  "curl -L -X POST $URL/api/reservations \
  -H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' \
  -d '{\"vehiculo_id\":$AVAILABLE_VEHICLE_ID,\"sede_id\":1,\
\"origen_direccion\":\"Sede Cabecera\",\"destino_direccion\":\"Sede Cabecera\",\
\"fecha_inicio\":\"2025-11-15 10:00:00\",\"fecha_fin\":\"2025-11-15 18:00:00\"}'"
fi

test_request "Ver catálogo de vehículos" \
"curl -L -X GET $URL/api/vehicles -H 'Accept: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN'"

test_request "Verificar disponibilidad de vehículo" \
"curl -L -X POST $URL/api/vehicles/1/check-availability \
-H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' \
-d '{\"fecha_inicio\":\"2025-11-16 09:00:00\",\"fecha_fin\":\"2025-11-16 17:00:00\"}'"

test_request "Crear domicilio de paquete" \
"curl -L -X POST $URL/api/deliveries/package \
-H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' \
-d '{
  \"sede_id\": 1,
  \"direccion_origen\": \"Calle 45 #27-10, Bucaramanga\",
  \"lat_origen\": 7.119349,
  \"lon_origen\": -73.122742,
  \"direccion_destino\": \"Carrera 27 #34-20, Bucaramanga\",
  \"lat_destino\": 7.125000,
  \"lon_destino\": -73.120000,
  \"nombre_remitente\": \"Juan Pérez\",
  \"telefono_remitente\": \"+57 300 123 4567\",
  \"nombre_destinatario\": \"María García\",
  \"telefono_destinatario\": \"+57 301 987 6543\",
  \"descripcion_contenido\": \"Documentos importantes\",
  \"peso_estimado\": 2.5,
  \"es_fragil\": false,
  \"requiere_firma\": true
}'"

test_request "Ver mis domicilios" \
"curl -L -X GET $URL/api/deliveries/me/list -H 'Accept: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN'"

test_request "Intentar asignar conductor (403 esperado)" \
"curl -L -X POST $URL/api/deliveries/1/assign-driver \
-H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' \
-d '{\"conductor_id\":3}'"

separator

# ==========================================================
# 🚗 CONDUCTOR
# ==========================================================
echo -e "${BLUE}🚗 TESTS CONDUCTOR${RESET}"

set +e
DELIVERY_ID=$(curl -s -H "Accept: application/json" -H "Authorization: Bearer $CONDUCTOR_TOKEN" \
"$URL/api/deliveries/me/assigned" | jq -r '.data[0].id // empty' 2>/dev/null)
set -e

if [ -z "$DELIVERY_ID" ] || [ "$DELIVERY_ID" = "null" ]; then
  echo -e "${YELLOW}⚠️  No se encontró entrega asignada al conductor. Saltando tests de entrega.${RESET}"
else
  test_request "Ver entregas asignadas a mí" \
  "curl -L -X GET $URL/api/deliveries/me/assigned -H 'Accept: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN'"

  test_request "Iniciar entrega asignada" \
  "curl -L -X POST $URL/api/deliveries/$DELIVERY_ID/start -H 'Accept: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN'"

  test_request "Completar entrega" \
  "curl -L -X POST $URL/api/deliveries/$DELIVERY_ID/complete \
  -H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN' \
  -d '{\"notas\":\"Entrega exitosa sin novedad\"}'"

  test_request "Tracking de entrega" \
  "curl -L -X GET $URL/api/deliveries/$DELIVERY_ID/track -H 'Accept: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN'"
fi

separator

# ==========================================================
# 🌐 PÚBLICOS
# ==========================================================
echo -e "${BLUE}🌐 TESTS PÚBLICOS (sin token)${RESET}"
test_request "Estado del API" "curl -L -X GET $URL/api/status -H 'Accept: application/json'"
test_request "Listar vehículos (público)" "curl -L -X GET $URL/api/vehicles -H 'Accept: application/json'"
test_request "Ver detalle de vehículo" "curl -L -X GET $URL/api/vehicles/1 -H 'Accept: application/json'"
test_request "Listar sedes" "curl -L -X GET $URL/api/branches -H 'Accept: application/json'"
test_request "Ver una sede" "curl -L -X GET $URL/api/branches/1 -H 'Accept: application/json'"
test_request "Buscar sede más cercana" \
"curl -L -X POST $URL/api/branches/nearest -H 'Accept: application/json' -H 'Content-Type: application/json' \
-d '{\"lat\":7.119349,\"lon\":-73.122742}'"
test_request "Métodos de pago disponibles" "curl -L -X GET $URL/api/payments/methods/available -H 'Accept: application/json'"
test_request "Estadísticas generales (telemetría)" "curl -L -X GET $URL/api/estadisticas -H 'Accept: application/json'"
test_request "Últimas posiciones GPS" "curl -L -X GET $URL/api/telemetria/latest -H 'Accept: application/json'"

separator
echo -e "✅ ${GREEN}PRUEBAS COMPLETADAS${RESET}"
echo -e "${GRAY}Archivo tokens usado: $TOKENS_FILE${RESET}"
echo -e "${GRAY}Log completo: $LOG_FILE${RESET}\n"