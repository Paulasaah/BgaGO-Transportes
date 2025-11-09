import json
import math
import time
import threading
import paho.mqtt.publish as publish
import random
import os
from datetime import datetime

# =======================================================
# CONFIGURACIÓN
# =======================================================
BROKER_HOST = os.getenv("BROKER_HOST", "127.0.0.1")
BROKER_PORT = int(os.getenv("BROKER_PORT", 1883))
TOPIC_TEMPLATE = "vehiculos/{device_id}/telemetria"
UPDATE_INTERVAL = 1.5

# =======================================================
# SEDES DE BUCARAMANGA
# =======================================================
BRANCHES = {
    "Cabecera": {
        "lat": 7.1193, 
        "lon": -73.1227, 
        "radio": 0.004,
        "color": "#3b82f6"
    },
    "Centro": {
        "lat": 7.1254, 
        "lon": -73.1198, 
        "radio": 0.0035,
        "color": "#10b981"
    },
    "Floridablanca": {
        "lat": 7.0621, 
        "lon": -73.0873, 
        "radio": 0.004,
        "color": "#f59e0b"
    },
    "Cañaveral": {
        "lat": 7.0893, 
        "lon": -73.1074, 
        "radio": 0.003,
        "color": "#8b5cf6"
    }
}

# =======================================================
# DISPOSITIVOS
# =======================================================
DEVICES = {
    # === VEHÍCULOS ===
    "VH-001": {
        "type": "vehiculo",
        "status": "idle",
        "battery": 95.0,
        "battery_health": 98.5,
        "current_branch": "Cabecera",
        "target_branch": None,
        "speed_base": 0.00025,
        "discharge_rate": 0.03,
        "odometer": 1250.5,
        "trip_count": 45,
        "maintenance_km_left": 750.0,
        "last_maintenance": "2024-10-15",
        "idle_time": 0,
    },
    "VH-002": {
        "type": "vehiculo",
        "status": "idle",
        "battery": 82.0,
        "battery_health": 95.0,
        "current_branch": "Centro",
        "target_branch": None,
        "speed_base": 0.0003,
        "discharge_rate": 0.04,
        "odometer": 2100.3,
        "trip_count": 78,
        "maintenance_km_left": 400.0,
        "last_maintenance": "2024-09-20",
        "idle_time": 0,
    },
    "VH-003": {
        "type": "vehiculo",
        "status": "idle",
        "battery": 68.0,
        "battery_health": 88.0,
        "current_branch": "Floridablanca",
        "target_branch": None,
        "speed_base": 0.00028,
        "discharge_rate": 0.035,
        "odometer": 3800.7,
        "trip_count": 125,
        "maintenance_km_left": 80.0,
        "last_maintenance": "2024-08-10",
        "idle_time": 0,
    },
    "VH-004": {
        "type": "vehiculo",
        "status": "charging",
        "battery": 35.0,
        "battery_health": 92.0,
        "current_branch": "Cañaveral",
        "target_branch": None,
        "speed_base": 0.0,
        "discharge_rate": 0.0,
        "odometer": 1850.2,
        "trip_count": 62,
        "maintenance_km_left": 650.0,
        "last_maintenance": "2024-10-01",
        "idle_time": 0,
    },
    
    # === CONDUCTORES ===
    "CD-001": {
        "type": "conductor",
        "status": "idle",
        "driver_name": "Carlos Mendoza",
        "battery": 88.5,
        "battery_health": 94.0,
        "rating": 4.8,
        "deliveries_completed": 234,
        "current_branch": "Cabecera",
        "delivery_route": [],
        "current_delivery_index": 0,
        "speed_base": 0.0004,
        "discharge_rate": 0.06,
        "odometer": 4250.8,
        "trip_count": 234,
        "maintenance_km_left": 250.0,
        "last_maintenance": "2024-10-20",
        "idle_time": 0,
        "phone": "+57 301 234 5678",
        "vehicle_type": "Moto eléctrica"
    },
    "CD-002": {
        "type": "conductor",
        "status": "idle",
        "driver_name": "María Torres",
        "battery": 91.2,
        "battery_health": 96.5,
        "rating": 4.9,
        "deliveries_completed": 189,
        "current_branch": "Centro",
        "delivery_route": [],
        "current_delivery_index": 0,
        "speed_base": 0.00045,
        "discharge_rate": 0.07,
        "odometer": 3520.4,
        "trip_count": 189,
        "maintenance_km_left": 480.0,
        "last_maintenance": "2024-10-10",
        "idle_time": 0,
        "phone": "+57 312 987 6543",
        "vehicle_type": "Bicicleta eléctrica"
    },
    "CD-003": {
        "type": "conductor",
        "status": "idle",
        "driver_name": "Juan Ramírez",
        "battery": 75.0,
        "battery_health": 68.0,
        "rating": 4.6,
        "deliveries_completed": 156,
        "current_branch": "Floridablanca",
        "delivery_route": [],
        "current_delivery_index": 0,
        "speed_base": 0.00042,
        "discharge_rate": 0.065,
        "odometer": 5200.1,
        "trip_count": 156,
        "maintenance_km_left": 45.0,
        "last_maintenance": "2024-09-15",
        "idle_time": 0,
        "phone": "+57 320 555 1234",
        "vehicle_type": "Moto eléctrica"
    },
    "CD-004": {
        "type": "conductor",
        "status": "active",
        "driver_name": "Ana Martínez",
        "battery": 82.0,
        "battery_health": 91.0,
        "rating": 4.7,
        "deliveries_completed": 178,
        "current_branch": "Cañaveral",
        "delivery_route": [],
        "current_delivery_index": 0,
        "speed_base": 0.00038,
        "discharge_rate": 0.055,
        "odometer": 2890.5,
        "trip_count": 178,
        "maintenance_km_left": 710.0,
        "last_maintenance": "2024-10-05",
        "idle_time": 0,
        "phone": "+57 315 444 7890",
        "vehicle_type": "Scooter eléctrico"
    }
}

# =======================================================
# FUNCIONES AUXILIARES
# =======================================================
def distance(lat1, lon1, lat2, lon2):
    dist_degrees = math.sqrt((lat2 - lat1) ** 2 + (lon2 - lon1) ** 2)
    return dist_degrees * 111

def move_towards(current_lat, current_lon, target_lat, target_lon, speed):
    dist = distance(current_lat, current_lon, target_lat, target_lon)
    
    if dist < 0.03:
        return target_lat, target_lon, True, 0
    
    speed_kmh = (speed * 111) * (3600 / UPDATE_INTERVAL)
    ratio = min(speed / (dist / 111), 0.8)
    new_lat = current_lat + (target_lat - current_lat) * ratio
    new_lon = current_lon + (target_lon - current_lon) * ratio
    
    return new_lat, new_lon, False, speed_kmh

def get_random_point_in_branch(branch_name):
    branch = BRANCHES[branch_name]
    angle = random.uniform(0, 2 * math.pi)
    radius = random.uniform(0, branch["radio"] * 0.7)
    
    lat = branch["lat"] + radius * math.cos(angle)
    lon = branch["lon"] + radius * math.sin(angle)
    
    return lat, lon

def generate_delivery_route(start_branch):
    route = []
    start = BRANCHES[start_branch]
    
    num_deliveries = random.randint(3, 5)
    
    for i in range(num_deliveries):
        angle = random.uniform(0, 2 * math.pi)
        distance = random.uniform(0.01, 0.03)
        
        lat = start["lat"] + distance * math.cos(angle)
        lon = start["lon"] + distance * math.sin(angle)
        
        route.append({
            "lat": lat,
            "lon": lon,
            "name": f"Entrega #{i+1}",
            "customer": f"Cliente-{random.randint(100, 999)}",
            "wait_time": random.uniform(3, 8)
        })
    
    route.append({
        "lat": start["lat"],
        "lon": start["lon"],
        "name": f"Retorno {start_branch}",
        "customer": "Sede",
        "wait_time": 0
    })
    
    return route

def init_device_positions():
    for device_id, device in DEVICES.items():
        device["lat"], device["lon"] = get_random_point_in_branch(device["current_branch"])
        
        if device_id == "CD-004" and device["status"] == "active":
            device["delivery_route"] = generate_delivery_route(device["current_branch"])
            device["current_delivery_index"] = 0
            print(f"[{device_id}] Ruta inicial: {len(device['delivery_route'])-1} entregas")

# =======================================================
# SIMULADOR DE VEHÍCULO
# =======================================================
def simulate_vehicle(device_id):
    device = DEVICES[device_id]
    print(f"[{device_id}] Vehiculo iniciado en {device['current_branch']} - {device['status']}")
    
    while True:
        try:
            current_speed = 0
            
            if device["status"] == "charging":
                if random.random() < 0.05:
                    device["lat"], device["lon"] = get_random_point_in_branch(device["current_branch"])
                
                device["battery"] = min(100, device["battery"] + 1.2)
                
                if device["battery"] >= 95:
                    device["status"] = "idle"
                    device["idle_time"] = 0
                    print(f"[{device_id}] Carga completa -> idle")
            
            elif device["status"] == "maintenance":
                device["idle_time"] += UPDATE_INTERVAL
                
                if random.random() < 0.03:
                    device["lat"], device["lon"] = get_random_point_in_branch(device["current_branch"])
                
                if device["idle_time"] >= 45:
                    device["maintenance_km_left"] = 1000.0
                    device["battery_health"] = min(100, device["battery_health"] + 8)
                    device["status"] = "idle"
                    device["idle_time"] = 0
                    device["last_maintenance"] = datetime.now().strftime("%Y-%m-%d")
                    print(f"[{device_id}] Mantenimiento completado -> idle")
            
            elif device["status"] == "idle":
                if random.random() < 0.08:
                    device["lat"], device["lon"] = get_random_point_in_branch(device["current_branch"])
                
                device["battery"] -= 0.008
                device["idle_time"] += UPDATE_INTERVAL
                
                if device["maintenance_km_left"] <= 50 or device["battery_health"] <= 70:
                    device["status"] = "maintenance"
                    device["idle_time"] = 0
                    print(f"[{device_id}] -> maintenance")
                
                elif device["idle_time"] >= random.randint(10, 20):
                    if device["battery"] > 25:
                        branches_list = [b for b in BRANCHES.keys() if b != device["current_branch"]]
                        device["target_branch"] = random.choice(branches_list)
                        device["status"] = "active"
                        device["idle_time"] = 0
                        print(f"[{device_id}] Viaje: {device['current_branch']} -> {device['target_branch']}")
                    else:
                        device["status"] = "charging"
                        print(f"[{device_id}] Bateria baja -> charging")
            
            elif device["status"] == "active" and device["target_branch"]:
                target_branch = BRANCHES[device["target_branch"]]
                
                device["lat"], device["lon"], arrived, current_speed = move_towards(
                    device["lat"], device["lon"],
                    target_branch["lat"], target_branch["lon"],
                    device["speed_base"]
                )
                
                device["battery"] -= device["discharge_rate"]
                
                km_traveled = (device["speed_base"] * 111) * (UPDATE_INTERVAL / 3600)
                device["odometer"] += km_traveled
                device["maintenance_km_left"] -= km_traveled
                device["battery_health"] -= 0.0008
                
                if arrived:
                    device["current_branch"] = device["target_branch"]
                    device["target_branch"] = None
                    device["trip_count"] += 1
                    
                    if device["battery"] < 25:
                        device["status"] = "charging"
                        print(f"[{device_id}] Llego a {device['current_branch']} -> charging")
                    elif device["maintenance_km_left"] <= 50:
                        device["status"] = "maintenance"
                        print(f"[{device_id}] Llego a {device['current_branch']} -> maintenance")
                    else:
                        device["status"] = "idle"
                        print(f"[{device_id}] Llego a {device['current_branch']} -> idle")
            
            payload = {
                "device_id": device_id,
                "device_type": device["type"],
                "status": device["status"],
                "Geopoint": {
                    "lat": round(device["lat"], 8),
                    "lon": round(device["lon"], 8),
                    "alt": 0
                },
                "Battery": round(device["battery"], 1),
                "battery_health": round(device["battery_health"], 1),
                "speed": round(current_speed, 1),
                "current_branch": device["current_branch"],
                "target_branch": device["target_branch"],
                "odometer": round(device["odometer"], 2),
                "trip_count": device["trip_count"],
                "maintenance_km_left": round(device["maintenance_km_left"], 2),
                "last_maintenance": device["last_maintenance"],
                "timestamp": datetime.now().isoformat()
            }
            
            publish.single(
                TOPIC_TEMPLATE.format(device_id=device_id),
                json.dumps(payload),
                hostname=BROKER_HOST,
                port=BROKER_PORT
            )
            
            time.sleep(UPDATE_INTERVAL)
            
        except Exception as e:
            print(f"Error en {device_id}: {e}")
            time.sleep(3)

# =======================================================
# SIMULADOR DE CONDUCTOR
# =======================================================
def simulate_conductor(device_id):
    device = DEVICES[device_id]
    print(f"[{device_id}] {device['driver_name']} iniciado en {device['current_branch']}")
    
    waiting_at_delivery = False
    wait_start_time = 0
    
    while True:
        try:
            current_speed = 0
            
            if device["status"] == "idle":
                if random.random() < 0.12:
                    device["lat"], device["lon"] = get_random_point_in_branch(device["current_branch"])
                
                device["battery"] -= 0.015
                device["idle_time"] += UPDATE_INTERVAL
                
                if device["maintenance_km_left"] <= 50 or device["battery_health"] <= 70:
                    device["status"] = "maintenance"
                    device["idle_time"] = 0
                    print(f"[{device_id}] {device['driver_name']} -> maintenance")
                
                elif device["battery"] < 20:
                    device["status"] = "charging"
                    device["idle_time"] = 0
                    print(f"[{device_id}] {device['driver_name']} -> charging")
                
                elif device["idle_time"] >= random.randint(12, 25):
                    device["delivery_route"] = generate_delivery_route(device["current_branch"])
                    device["current_delivery_index"] = 0
                    device["status"] = "active"
                    device["idle_time"] = 0
                    print(f"[{device_id}] {device['driver_name']} - {len(device['delivery_route'])-1} entregas")
            
            elif device["status"] == "charging":
                if random.random() < 0.05:
                    device["lat"], device["lon"] = get_random_point_in_branch(device["current_branch"])
                
                device["battery"] = min(100, device["battery"] + 1.5)
                
                if device["battery"] >= 95:
                    device["status"] = "idle"
                    device["idle_time"] = 0
                    print(f"[{device_id}] {device['driver_name']} carga completa -> idle")
            
            elif device["status"] == "maintenance":
                device["idle_time"] += UPDATE_INTERVAL
                
                if random.random() < 0.04:
                    device["lat"], device["lon"] = get_random_point_in_branch(device["current_branch"])
                
                if device["idle_time"] >= 40:
                    device["maintenance_km_left"] = 1000.0
                    device["battery_health"] = min(100, device["battery_health"] + 10)
                    device["status"] = "idle"
                    device["idle_time"] = 0
                    device["last_maintenance"] = datetime.now().strftime("%Y-%m-%d")
                    print(f"[{device_id}] {device['driver_name']} mantenimiento completado")
            
            elif device["status"] == "active" and len(device["delivery_route"]) > 0:
                current_point = device["delivery_route"][device["current_delivery_index"]]
                
                if waiting_at_delivery:
                    elapsed_wait = time.time() - wait_start_time
                    
                    if elapsed_wait >= current_point["wait_time"]:
                        waiting_at_delivery = False
                        
                        if "Retorno" not in current_point["name"]:
                            device["deliveries_completed"] += 1
                            device["trip_count"] += 1
                            device["rating"] = min(5.0, max(3.0, device["rating"] + random.uniform(-0.05, 0.1)))
                            print(f"[{device_id}] {device['driver_name']} - {current_point['name']} completada")
                        
                        device["current_delivery_index"] += 1
                        
                        if device["current_delivery_index"] >= len(device["delivery_route"]):
                            device["status"] = "idle"
                            device["current_delivery_index"] = 0
                            device["delivery_route"] = []
                            print(f"[{device_id}] {device['driver_name']} - Ruta completada")
                    else:
                        if random.random() < 0.1:
                            device["lat"] += random.uniform(-0.00002, 0.00002)
                            device["lon"] += random.uniform(-0.00002, 0.00002)
                
                else:
                    device["lat"], device["lon"], arrived, current_speed = move_towards(
                        device["lat"], device["lon"],
                        current_point["lat"], current_point["lon"],
                        device["speed_base"]
                    )
                    
                    device["battery"] -= device["discharge_rate"]
                    
                    km_traveled = (device["speed_base"] * 111) * (UPDATE_INTERVAL / 3600)
                    device["odometer"] += km_traveled
                    device["maintenance_km_left"] -= km_traveled
                    device["battery_health"] -= 0.0006
                    
                    if arrived:
                        waiting_at_delivery = True
                        wait_start_time = time.time()
                        print(f"[{device_id}] {device['driver_name']} - Llego a {current_point['name']}")
            
            payload = {
                "device_id": device_id,
                "device_type": device["type"],
                "status": device["status"],
                "Geopoint": {
                    "lat": round(device["lat"], 8),
                    "lon": round(device["lon"], 8),
                    "alt": 0
                },
                "Battery": round(device["battery"], 1),
                "battery_health": round(device["battery_health"], 1),
                "speed": round(current_speed, 1),
                "current_branch": device["current_branch"],
                "odometer": round(device["odometer"], 2),
                "trip_count": device["trip_count"],
                "maintenance_km_left": round(device["maintenance_km_left"], 2),
                "last_maintenance": device["last_maintenance"],
                "driver_name": device["driver_name"],
                "deliveries_completed": device["deliveries_completed"],
                "rating": round(device["rating"], 2),
                "phone": device.get("phone", ""),
                "vehicle_type": device.get("vehicle_type", ""),
                "timestamp": datetime.now().isoformat()
            }
            
            publish.single(
                TOPIC_TEMPLATE.format(device_id=device_id),
                json.dumps(payload),
                hostname=BROKER_HOST,
                port=BROKER_PORT
            )
            
            time.sleep(UPDATE_INTERVAL)
            
        except Exception as e:
            print(f"Error en {device_id}: {e}")
            time.sleep(3)

# =======================================================
# INICIO
# =======================================================
if __name__ == "__main__":
    print("=" * 80)
    print("SIMULADOR MQTT - BgaGO v3.0")
    print("=" * 80)
    print(f"Broker: {BROKER_HOST}:{BROKER_PORT}")
    print(f"Intervalo: {UPDATE_INTERVAL}s")
    print(f"Vehiculos: {len([d for d in DEVICES.values() if d['type'] == 'vehiculo'])}")
    print(f"Conductores: {len([d for d in DEVICES.values() if d['type'] == 'conductor'])}")
    print(f"Sedes: {', '.join(BRANCHES.keys())}")
    print("=" * 80)
    
    init_device_positions()
    
    threads = []
    for device_id, device in DEVICES.items():
        if device["type"] == "vehiculo":
            t = threading.Thread(target=simulate_vehicle, args=(device_id,), daemon=True)
        else:
            t = threading.Thread(target=simulate_conductor, args=(device_id,), daemon=True)
        
        t.start()
        threads.append(t)
        time.sleep(0.3)
    
    print("\nTodos los dispositivos iniciados")
    print("Monitoreando en tiempo real...")
    print("Presiona Ctrl+C para detener\n")
    
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\nSimulacion detenida")