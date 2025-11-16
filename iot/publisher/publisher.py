import json
import math
import time
import threading
import paho.mqtt.publish as publish
import random
import os
import sys
from datetime import datetime

# Forzar unbuffered output para Docker
sys.stdout = os.fdopen(sys.stdout.fileno(), 'w', buffering=1)
sys.stderr = os.fdopen(sys.stderr.fileno(), 'w', buffering=1)

# CONFIGURACIÓN
BROKER_HOST = os.getenv("BROKER_HOST", "127.0.0.1")
BROKER_PORT = int(os.getenv("BROKER_PORT", 1883))
TOPIC_TEMPLATE = "vehiculos/{device_id}/telemetria"
UPDATE_INTERVAL = 2.0

# SEDES DE BUCARAMANGA
BRANCHES = {
    "Cabecera": {"lat": 7.1193, "lon": -73.1227, "radio": 0.004},
    "Centro": {"lat": 7.1254, "lon": -73.1198, "radio": 0.0035},
    "Floridablanca": {"lat": 7.0621, "lon": -73.0873, "radio": 0.004}
}

# DISPOSITIVOS (5 dispositivos para mejor rendimiento)
DEVICES = {
    "VH-001": {
        "type": "vehiculo", "status": "idle", "battery": 95.0, "battery_health": 98.5,
        "current_branch": "Cabecera", "target_branch": None, "speed_base": 0.00025,
        "discharge_rate": 0.03, "odometer": 1250.5, "trip_count": 45,
        "maintenance_km_left": 750.0, "last_maintenance": "2024-10-15", "idle_time": 0,
    },
    "VH-002": {
        "type": "vehiculo", "status": "idle", "battery": 25.0, "battery_health": 95.0,
        "current_branch": "Centro", "target_branch": None, "speed_base": 0.0003,
        "discharge_rate": 0.04, "odometer": 2100.3, "trip_count": 78,
        "maintenance_km_left": 400.0, "last_maintenance": "2024-09-20", "idle_time": 0,
    },
    "CD-001": {
        "type": "conductor", "status": "idle", "driver_name": "Carlos Mendoza",
        "battery": 88.5, "battery_health": 94.0, "rating": 4.8, "deliveries_completed": 234,
        "current_branch": "Cabecera", "delivery_route": [], "current_delivery_index": 0,
        "speed_base": 0.0004, "discharge_rate": 0.06, "odometer": 4250.8, "trip_count": 234,
        "maintenance_km_left": 250.0, "last_maintenance": "2024-10-20", "idle_time": 0,
        "phone": "+57 301 234 5678", "vehicle_type": "Moto eléctrica", "waiting": False, "wait_start": 0
    },
    "CD-002": {
        "type": "conductor", "status": "idle", "driver_name": "María Torres",
        "battery": 91.2, "battery_health": 96.5, "rating": 4.9, "deliveries_completed": 189,
        "current_branch": "Centro", "delivery_route": [], "current_delivery_index": 0,
        "speed_base": 0.00045, "discharge_rate": 0.07, "odometer": 3520.4, "trip_count": 189,
        "maintenance_km_left": 480.0, "last_maintenance": "2024-10-10", "idle_time": 0,
        "phone": "+57 312 987 6543", "vehicle_type": "Bicicleta eléctrica", "waiting": False, "wait_start": 0
    },
    "CD-003": {
        "type": "conductor", "status": "idle", "driver_name": "Juan Ramírez",
        "battery": 75.0, "battery_health": 68.0, "rating": 4.6, "deliveries_completed": 156,
        "current_branch": "Floridablanca", "delivery_route": [], "current_delivery_index": 0,
        "speed_base": 0.00042, "discharge_rate": 0.065, "odometer": 5200.1, "trip_count": 156,
        "maintenance_km_left": 45.0, "last_maintenance": "2024-09-15", "idle_time": 0,
        "phone": "+57 320 555 1234", "vehicle_type": "Moto eléctrica", "waiting": False, "wait_start": 0
    }
}

def distance(lat1, lon1, lat2, lon2):
    return math.sqrt((lat2 - lat1) ** 2 + (lon2 - lon1) ** 2) * 111

def move_towards(current_lat, current_lon, target_lat, target_lon, speed):
    dist = distance(current_lat, current_lon, target_lat, target_lon)
    if dist < 0.025:
        return target_lat, target_lon, True, 0
    speed_kmh = (speed * 111) * (3600 / UPDATE_INTERVAL)
    ratio = min(speed / (dist / 111), 0.7)
    return current_lat + (target_lat - current_lat) * ratio, current_lon + (target_lon - current_lon) * ratio, False, speed_kmh

def get_random_point_in_branch(branch_name):
    branch = BRANCHES[branch_name]
    angle, radius = random.uniform(0, 2 * math.pi), random.uniform(0, branch["radio"] * 0.6)
    return branch["lat"] + radius * math.cos(angle), branch["lon"] + radius * math.sin(angle)

def generate_delivery_route(start_branch):
    route, start = [], BRANCHES[start_branch]
    for i in range(random.randint(2, 4)):
        angle, dist = random.uniform(0, 2 * math.pi), random.uniform(0.008, 0.025)
        route.append({"lat": start["lat"] + dist * math.cos(angle), "lon": start["lon"] + dist * math.sin(angle),
                     "name": f"Entrega #{i+1}", "customer": f"Cliente-{random.randint(100, 999)}", "wait_time": random.uniform(4, 9)})
    route.append({"lat": start["lat"], "lon": start["lon"], "name": f"Retorno {start_branch}", "customer": "Sede", "wait_time": 0})
    return route

def init_device_positions():
    for device_id, device in DEVICES.items():
        device["lat"], device["lon"] = get_random_point_in_branch(device["current_branch"])
        print(f"[{device_id}] Inicializado en {device['current_branch']}")

def simulate_vehicle(device_id):
    device = DEVICES[device_id]
    print(f"[{device_id}] Vehículo iniciado")
    
    while True:
        try:
            current_speed = 0
            
            if device["status"] == "charging":
                if random.random() < 0.03:
                    device["lat"] += random.uniform(-0.00001, 0.00001)
                    device["lon"] += random.uniform(-0.00001, 0.00001)
                device["battery"] = min(100, device["battery"] + 1.5)
                if device["battery"] >= 95:
                    device["status"] = "idle"
                    device["idle_time"] = 0
                    print(f"[{device_id}] Carga completa")
            
            elif device["status"] == "maintenance":
                device["idle_time"] += UPDATE_INTERVAL
                if random.random() < 0.02:
                    device["lat"] += random.uniform(-0.00001, 0.00001)
                    device["lon"] += random.uniform(-0.00001, 0.00001)
                if device["idle_time"] >= 60:
                    device["maintenance_km_left"] = 1000.0
                    device["battery_health"] = min(100, device["battery_health"] + 15)
                    device["status"] = "idle"
                    device["idle_time"] = 0
                    device["last_maintenance"] = datetime.now().strftime("%Y-%m-%d")
                    print(f"[{device_id}] Mantenimiento completado")
            
            elif device["status"] == "idle":
                if random.random() < 0.15:
                    angle = random.uniform(0, 2 * math.pi)
                    move_dist = random.uniform(0.00005, 0.0001)
                    device["lat"] += move_dist * math.cos(angle)
                    device["lon"] += move_dist * math.sin(angle)
                device["battery"] -= 0.01
                device["idle_time"] += UPDATE_INTERVAL
                
                if device["maintenance_km_left"] <= 50 or device["battery_health"] <= 70:
                    device["status"] = "maintenance"
                    device["idle_time"] = 0
                    print(f"[{device_id}] Mantenimiento requerido")
                elif device["battery"] < 25:
                    device["status"] = "charging"
                    device["idle_time"] = 0
                    print(f"[{device_id}] Batería baja")
                elif device["idle_time"] >= random.randint(15, 30):
                    branches_list = [b for b in BRANCHES.keys() if b != device["current_branch"]]
                    device["target_branch"] = random.choice(branches_list)
                    device["status"] = "active"
                    device["idle_time"] = 0
                    print(f"[{device_id}] Viaje: {device['current_branch']} -> {device['target_branch']}")
            
            elif device["status"] == "active" and device["target_branch"]:
                target_branch = BRANCHES[device["target_branch"]]
                device["lat"], device["lon"], arrived, current_speed = move_towards(
                    device["lat"], device["lon"], target_branch["lat"], target_branch["lon"], device["speed_base"])
                device["battery"] -= device["discharge_rate"]
                km_traveled = (device["speed_base"] * 111) * (UPDATE_INTERVAL / 3600)
                device["odometer"] += km_traveled
                device["maintenance_km_left"] -= km_traveled
                device["battery_health"] -= 0.001
                
                if arrived:
                    device["current_branch"] = device["target_branch"]
                    device["target_branch"] = None
                    device["trip_count"] += 1
                    if device["battery"] < 25:
                        device["status"] = "charging"
                    elif device["maintenance_km_left"] <= 50:
                        device["status"] = "maintenance"
                    else:
                        device["status"] = "idle"
                    print(f"[{device_id}] Llegó a {device['current_branch']}")
            
            payload = {
                "device_id": device_id, "device_type": device["type"], "status": device["status"],
                "Geopoint": {"lat": round(device["lat"], 8), "lon": round(device["lon"], 8), "alt": 0},
                "Battery": round(device["battery"], 1), "battery_health": round(device["battery_health"], 1),
                "speed": round(current_speed, 1), "current_branch": device["current_branch"],
                "target_branch": device["target_branch"], "odometer": round(device["odometer"], 2),
                "trip_count": device["trip_count"], "maintenance_km_left": round(device["maintenance_km_left"], 2),
                "last_maintenance": device["last_maintenance"], "timestamp": datetime.now().isoformat()
            }
            
            topic = TOPIC_TEMPLATE.format(device_id=device_id)
            publish.single(topic, json.dumps(payload), hostname=BROKER_HOST, port=BROKER_PORT)
            print(f"📡 [{device_id}] {device['status']} | Bat: {device['battery']:.1f}% | Pos: {device['lat']:.6f},{device['lon']:.6f}", flush=True)
            time.sleep(UPDATE_INTERVAL)
        except Exception as e:
            print(f"Error en {device_id}: {e}")
            time.sleep(5)

def simulate_conductor(device_id):
    device = DEVICES[device_id]
    print(f"[{device_id}] {device['driver_name']} iniciado")
    
    while True:
        try:
            current_speed = 0
            
            if device["status"] == "idle":
                if random.random() < 0.18:
                    angle = random.uniform(0, 2 * math.pi)
                    move_dist = random.uniform(0.00006, 0.00012)
                    device["lat"] += move_dist * math.cos(angle)
                    device["lon"] += move_dist * math.sin(angle)
                device["battery"] -= 0.02
                device["idle_time"] += UPDATE_INTERVAL
                
                if device["maintenance_km_left"] <= 50 or device["battery_health"] <= 70:
                    device["status"] = "maintenance"
                    device["idle_time"] = 0
                elif device["battery"] < 20:
                    device["status"] = "charging"
                    device["idle_time"] = 0
                elif device["idle_time"] >= random.randint(18, 35):
                    device["delivery_route"] = generate_delivery_route(device["current_branch"])
                    device["current_delivery_index"] = 0
                    device["status"] = "active"
                    device["idle_time"] = 0
                    device["waiting"] = False
                    print(f"[{device_id}] Ruta: {len(device['delivery_route'])-1} entregas")
            
            elif device["status"] == "charging":
                if random.random() < 0.04:
                    device["lat"] += random.uniform(-0.00001, 0.00001)
                    device["lon"] += random.uniform(-0.00001, 0.00001)
                device["battery"] = min(100, device["battery"] + 2.0)
                if device["battery"] >= 95:
                    device["status"] = "idle"
                    device["idle_time"] = 0
            
            elif device["status"] == "maintenance":
                device["idle_time"] += UPDATE_INTERVAL
                if random.random() < 0.03:
                    device["lat"] += random.uniform(-0.00001, 0.00001)
                    device["lon"] += random.uniform(-0.00001, 0.00001)
                if device["idle_time"] >= 50:
                    device["maintenance_km_left"] = 1000.0
                    device["battery_health"] = min(100, device["battery_health"] + 20)
                    device["status"] = "idle"
                    device["idle_time"] = 0
                    device["last_maintenance"] = datetime.now().strftime("%Y-%m-%d")
            
            elif device["status"] == "active" and len(device["delivery_route"]) > 0:
                current_point = device["delivery_route"][device["current_delivery_index"]]
                
                if device["waiting"]:
                    elapsed_wait = time.time() - device["wait_start"]
                    if random.random() < 0.2:
                        device["lat"] += random.uniform(-0.000015, 0.000015)
                        device["lon"] += random.uniform(-0.000015, 0.000015)
                    
                    if elapsed_wait >= current_point["wait_time"]:
                        device["waiting"] = False
                        if "Retorno" not in current_point["name"]:
                            device["deliveries_completed"] += 1
                            device["trip_count"] += 1
                            device["rating"] = min(5.0, max(3.5, device["rating"] + random.uniform(-0.08, 0.12)))
                        device["current_delivery_index"] += 1
                        if device["current_delivery_index"] >= len(device["delivery_route"]):
                            device["status"] = "idle"
                            device["current_delivery_index"] = 0
                            device["delivery_route"] = []
                            print(f"[{device_id}] Ruta completada")
                else:
                    device["lat"], device["lon"], arrived, current_speed = move_towards(
                        device["lat"], device["lon"], current_point["lat"], current_point["lon"], device["speed_base"])
                    device["battery"] -= device["discharge_rate"]
                    km_traveled = (device["speed_base"] * 111) * (UPDATE_INTERVAL / 3600)
                    device["odometer"] += km_traveled
                    device["maintenance_km_left"] -= km_traveled
                    device["battery_health"] -= 0.0008
                    if arrived:
                        device["waiting"] = True
                        device["wait_start"] = time.time()
            
            payload = {
                "device_id": device_id, "device_type": device["type"], "status": device["status"],
                "Geopoint": {"lat": round(device["lat"], 8), "lon": round(device["lon"], 8), "alt": 0},
                "Battery": round(device["battery"], 1), "battery_health": round(device["battery_health"], 1),
                "speed": round(current_speed, 1), "current_branch": device["current_branch"],
                "odometer": round(device["odometer"], 2), "trip_count": device["trip_count"],
                "maintenance_km_left": round(device["maintenance_km_left"], 2), "last_maintenance": device["last_maintenance"],
                "driver_name": device["driver_name"], "deliveries_completed": device["deliveries_completed"],
                "rating": round(device["rating"], 2), "phone": device.get("phone", ""),
                "vehicle_type": device.get("vehicle_type", ""), "timestamp": datetime.now().isoformat()
            }
            
            topic = TOPIC_TEMPLATE.format(device_id=device_id)
            publish.single(topic, json.dumps(payload), hostname=BROKER_HOST, port=BROKER_PORT)
            print(f"📡 [{device_id}] {device['driver_name']} | {device['status']} | Bat: {device['battery']:.1f}%", flush=True)
            time.sleep(UPDATE_INTERVAL)
        except Exception as e:
            print(f"Error en {device_id}: {e}")
            time.sleep(5)

if __name__ == "__main__":
    print("="*80)
    print("SIMULADOR MQTT - BgaGO v4.0")
    print("="*80)
    print(f"Broker: {BROKER_HOST}:{BROKER_PORT}")
    print(f"Dispositivos: {len(DEVICES)}")
    print("="*80)
    
    init_device_positions()
    
    threads = []
    for device_id, device in DEVICES.items():
        t = threading.Thread(target=simulate_vehicle if device["type"] == "vehiculo" else simulate_conductor, 
                            args=(device_id,), daemon=True)
        t.start()
        threads.append(t)
        time.sleep(0.5)
    
    print("\nTodos los dispositivos iniciados")
    print("Presiona Ctrl+C para detener\n")
    
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\nSimulación detenida")