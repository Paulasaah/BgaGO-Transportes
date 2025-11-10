import json
import time
import requests
import paho.mqtt.client as mqtt
import os

BROKER_HOST = os.getenv("BROKER_HOST", "mosquitto")
BROKER_PORT = int(os.getenv("BROKER_PORT", 1883))
TOPIC = "vehiculos/+/telemetria"
LARAVEL_API_URL = os.getenv("LARAVEL_API", "http://laravel.test/api/telemetria")




def on_connect(client, userdata, flags, rc):
    if rc == 0:
        print("✅ Conectado al broker MQTT")
        client.subscribe(TOPIC)
        print(f"📡 Suscrito a: {TOPIC}")
    else:
        print(f"❌ Error al conectar (código {rc})")

def on_message(client, userdata, msg):
    try:
        payload = json.loads(msg.payload.decode("utf-8"))
        
        # Extraer datos del payload
        geopoint = payload.get("Geopoint", {})
        
        # Preparar datos para Laravel
        data = {
            "device_id": payload.get("device_id"),
            "device_type": payload.get("device_type", "vehiculo"),
            "status": payload.get("status", "active"),
            "lat": float(geopoint.get("lat", 0)),
            "lon": float(geopoint.get("lon", 0)),
            "alt": geopoint.get("alt"),
            "battery": float(payload.get("Battery", 0)),
            "battery_health": float(payload.get("battery_health", 100)),
            "speed": float(payload.get("speed", 0)),
            "current_branch": payload.get("current_branch"),
            "target_branch": payload.get("target_branch"),
            "odometer": float(payload.get("odometer", 0)),
            "trip_count": int(payload.get("trip_count", 0)),
            "maintenance_km_left": float(payload.get("maintenance_km_left", 1000)),
            "last_maintenance": payload.get("last_maintenance"),
            "driver_name": payload.get("driver_name"),
            "deliveries_completed": int(payload.get("deliveries_completed", 0)),
            "rating": payload.get("rating"),
        }

        # Log mejorado
        status_emoji = {
            'active': '🚗',
            'idle': '⏸️',
            'charging': '🔋',
            'maintenance': '🔧'
        }
        emoji = status_emoji.get(data['status'], '📍')
        
        print(f"{emoji} [{data['device_id']}] {data['status']} | Bat: {data['battery']:.1f}% | Pos: ({data['lat']:.6f}, {data['lon']:.6f})")

        # Enviar a Laravel
        try:
            response = requests.post(LARAVEL_API_URL, json=data, timeout=5)
            if response.status_code in [200, 201]:
                print(f"   ✓ Enviado a Laravel (HTTP {response.status_code})")
            else:
                print(f"   ✗ Error Laravel: HTTP {response.status_code} - {response.text[:100]}")
        except requests.exceptions.RequestException as e:
            print(f"   ✗ Error conectando con Laravel: {e}")

    except json.JSONDecodeError as e:
        print(f"❌ Error decodificando JSON: {e}")
    except Exception as e:
        print(f"❌ Error procesando mensaje: {e}")

def on_disconnect(client, userdata, rc):
    print("⚠️ Desconectado del broker MQTT")
    if rc != 0:
        print(f"   Desconexión inesperada. Intentando reconectar...")

def main():
    print("=" * 70)
    print("🚀 SUSCRIPTOR DE TELEMETRÍA MQTT → LARAVEL")
    print("=" * 70)
    print(f"📡 Broker: {BROKER_HOST}:{BROKER_PORT}")
    print(f"🌐 API Laravel: {LARAVEL_API_URL}")
    print(f"📻 Escuchando: {TOPIC}")
    print("=" * 70)

    client = mqtt.Client()
    client.on_connect = on_connect
    client.on_message = on_message
    client.on_disconnect = on_disconnect

    # Reintentos de conexión
    connected = False
    retry_count = 0
    max_retries = 5
    
    while not connected and retry_count < max_retries:
        try:
            print(f"\n🔄 Intento de conexión {retry_count + 1}/{max_retries}...")
            client.connect(BROKER_HOST, BROKER_PORT, 60)
            connected = True
            print("✅ Conexión exitosa")
        except Exception as e:
            retry_count += 1
            print(f"❌ Error conectando: {e}")
            if retry_count < max_retries:
                wait_time = min(5 * retry_count, 30)
                print(f"⏳ Reintentando en {wait_time}s...")
                time.sleep(wait_time)
            else:
                print("❌ Máximo de reintentos alcanzado. Saliendo...")
                return

    print("\n✅ Sistema listo. Esperando mensajes MQTT...\n")
    client.loop_forever()

if __name__ == "__main__":
    main()