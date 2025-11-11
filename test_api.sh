#!/bin/bash
# ==========================================================
# 🔍 Test Script API BgaGO - Roles & Policies
# Autor: Santiago Cardona
# ==========================================================

# ----------------------------------------------------------
# Detección de entorno
# ----------------------------------------------------------
if [ -f /.dockerenv ]; then
  URL="http://laravel.test"
else
  URL="http://127.0.0.1:8000"
fi

# Archivo de tokens
TOKENS_FILE="storage/test-tokens.txt"

# ----------------------------------------------------------
# Validar existencia del archivo de tokens
# ----------------------------------------------------------
if [ ! -f "$TOKENS_FILE" ]; then
  echo "⚠️  Archivo de tokens no encontrado en: $TOKENS_FILE"
  echo "Crea el archivo con el formato:"
  echo "ADMIN: <token>"
  echo "CLIENTE: <token>"
  echo "CONDUCTOR: <token>"
  exit 1
fi

# Leer tokens
ADMIN_TOKEN=$(grep "ADMIN:" "$TOKENS_FILE" | awk '{print $2}')
CLIENTE_TOKEN=$(grep "CLIENTE:" "$TOKENS_FILE" | awk '{print $2}')
CONDUCTOR_TOKEN=$(grep "CONDUCTOR:" "$TOKENS_FILE" | awk '{print $2}')

# ----------------------------------------------------------
# Colores y formato
# ----------------------------------------------------------
GREEN="\e[32m"
RED="\e[31m"
BLUE="\e[34m"
YELLOW="\e[33m"
GRAY="\e[90m"
RESET="\e[0m"

separator() {
  echo -e "\n${GRAY}-------------------------------------------------------${RESET}\n"
}

# ----------------------------------------------------------
# Función de prueba
# ----------------------------------------------------------
test_request() {
  local desc=$1
  local cmd=$2

  echo -e "${BLUE}▶️  $desc${RESET}"

  # Ejecutar la solicitud
  response=$(eval "$cmd -s -w '\n%{http_code}'")
  body=$(echo "$response" | head -n -1)
  code=$(echo "$response" | tail -n 1)

  # Extraer mensaje principal
  message=$(echo "$body" | jq -r '.message // .error // .detail // .status // empty')

  # Mostrar resultado
  if [[ "$code" =~ ^2 ]]; then
    echo -e "  ${GREEN}✔️  OK ($code)${RESET} ${GRAY}${message}${RESET}"
  elif [[ "$code" == "403" ]]; then
    echo -e "  ${RED}❌  FORBIDDEN ($code)${RESET} ${GRAY}${message}${RESET}"
  elif [[ "$code" == "401" ]]; then
    echo -e "  ${RED}❌  UNAUTHORIZED ($code)${RESET} ${GRAY}${message}${RESET}"
  else
    echo -e "  ${RED}⚠️  ERROR ($code)${RESET} ${GRAY}${message}${RESET}"
  fi

  # Mostrar cuerpo completo si está activo VERBOSE=1
  if [ "$VERBOSE" == "1" ]; then
    echo -e "${YELLOW}--- Respuesta completa ---${RESET}"
    echo "$body" | jq
  fi
}

# ----------------------------------------------------------
# Inicio
# ----------------------------------------------------------
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
"curl -X POST $URL/api/reservations/1/cancel -H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN' -H 'Content-Type: application/json' -d '{\"motivo\": \"Usuario no se presentó\"}'"

test_request "Ver todos los pagos" \
"curl -X GET $URL/api/payments -H 'Accept: application/json' -H 'Authorization: Bearer $ADMIN_TOKEN'"

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
"curl -X POST $URL/api/reservations -H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' -d '{\"vehicle_id\":1,\"sede_id\":1,\"fecha_inicio\":\"2025-11-15 10:00:00\",\"fecha_fin\":\"2025-11-15 18:00:00\"}'"

test_request "Intentar asignar conductor (403 esperado)" \
"curl -X POST $URL/api/deliveries/1/assign-driver -H 'Accept: application/json' -H 'Content-Type: application/json' -H 'Authorization: Bearer $CLIENTE_TOKEN' -d '{\"conductor_id\":2}'"

separator

# ==========================================================
# 🚗 CONDUCTOR
# ==========================================================
echo -e "${BLUE}🚗 TESTS CONDUCTOR${RESET}"

test_request "Ver entregas asignadas" \
"curl -X GET $URL/api/deliveries -H 'Accept: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN'"

test_request "Iniciar entrega" \
"curl -X POST $URL/api/deliveries/1/start -H 'Accept: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN'"

test_request "Completar entrega" \
"curl -X POST $URL/api/deliveries/1/complete -H 'Accept: application/json' -H 'Authorization: Bearer $CONDUCTOR_TOKEN'"

separator

# ==========================================================
# 🌐 PÚBLICOS
# ==========================================================
echo -e "${BLUE}🌐 TESTS PÚBLICOS (sin token)${RESET}"

test_request "Listar vehículos (público)" \
"curl -X GET $URL/api/vehicles -H 'Accept: application/json'"

test_request "Ver una sede (público)" \
"curl -X GET $URL/api/branches/1 -H 'Accept: application/json'"

separator
echo -e "✅ ${GREEN}PRUEBAS COMPLETADAS${RESET}"
echo -e "${GRAY}Archivo tokens usado: $TOKENS_FILE${RESET}\n"
