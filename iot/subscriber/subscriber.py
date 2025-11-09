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
        print("Conectado al broker MQTT")
        client.subscribe(TOPIC)
        print(f"Suscrito a: {TOPIC}")
    else:
        print(f"Error al conectar (código {rc})")

def on_message(client, userdata, msg):
    try:
        payload = json.loads(msg.payload.decode("utf-8"))
        
        # Extraer datos del payload
        geopoint = payload.get("Geopoint", {})
        
        data = {
            "device_id": payload.get("device_id"),
            "device_type": payload.get("device_type", "vehiculo"),
            "status": payload.get("status", "active"),
            "lat": round(float(geopoint.get("lat", 0)), 8),
            "lon": round(float(geopoint.get("lon", 0)), 8),
            "alt": geopoint.get("alt"),
            "battery": round(float(payload.get("Battery", 0)), 1),
            "battery_health": round(float(payload.get("battery_health", 100)), 1),
            "speed": round(float(payload.get("speed", 0)), 1),
            "current_branch": payload.get("current_branch"),
            "target_branch": payload.get("target_branch"),
            "odometer": round(float(payload.get("odometer", 0)), 2),
            "trip_count": int(payload.get("trip_count", 0)),
            "maintenance_km_left": round(float(payload.get("maintenance_km_left", 1000)), 2),
            "last_maintenance": payload.get("last_maintenance"),
            "driver_name": payload.get("driver_name"),
            "deliveries_completed": int(payload.get("deliveries_completed", 0)),
            "rating": payload.get("rating"),
        }

        print(f"[{msg.topic}] {data['device_id']} - {data['status']} - {data['battery']}%")

        try:
            response = requests.post(LARAVEL_API_URL, json=data, timeout=3)
            if response.status_code in [200, 201]:
                print(f"Enviado a Laravel ({response.status_code})")
            else:
                print(f"Error Laravel: {response.status_code}")
        except requests.exceptions.RequestException as e:
            print(f"Error conectando con Laravel: {e}")

    except Exception as e:
        print(f"Error procesando mensaje: {e}")

def on_disconnect(client, userdata, rc):
    print("Desconectado del broker MQTT")

def main():
    print("=" * 70)
    print("SUSCRIPTOR DE TELEMETRÍA MQTT → LARAVEL")
    print("=" * 70)
    print(f"Broker: {BROKER_HOST}:{BROKER_PORT}")
    print(f"API Laravel: {LARAVEL_API_URL}")
    print(f"Escuchando: {TOPIC}")
    print("=" * 70)

    client = mqtt.Client()
    client.on_connect = on_connect
    client.on_message = on_message
    client.on_disconnect = on_disconnect

    connected = False
    while not connected:
        try:
            client.connect(BROKER_HOST, BROKER_PORT, 60)
            connected = True
        except Exception as e:
            print(f"Error conectando: {e}")
            time.sleep(5)

    client.loop_forever()

if __name__ == "__main__":
    main()