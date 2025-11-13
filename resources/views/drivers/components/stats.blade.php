<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    <x-stats.card
        title="Reservas activas"
        :value="$activeCount"
        icon="calendar"
        color="blue"
    />
    <x-stats.card
        title="Entregas completadas hoy"
        :value="$completedToday"
        icon="check-circle-2"
        color="green"
    />
    <x-stats.card
        title="Vehículo asignado"
        :value="is_array($assignedVehicle) ? ($assignedVehicle['placa'] ?? 'Sin asignar') : ($assignedVehicle?->placa ?? 'Sin asignar')"
        icon="truck"
        color="purple"
    />
    <x-stats.card
        title="Estado de conexión"
        :value="$online ? 'En línea' : 'Desconectado'"
        icon="wifi"
        :color="$online ? 'green' : 'red'"
    />
</div>
