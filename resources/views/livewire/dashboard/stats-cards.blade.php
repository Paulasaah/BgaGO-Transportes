<x-dashboard.stats-grid>
    <x-stats.card
        title="Reservas Activas"
        :value="$reservasActivas"
        change="+5 desde ayer"
        icon="clipboard-document-list"
        color="blue"
    />

    <x-stats.card
        title="Domicilios Hoy"
        :value="$domiciliosHoy"
        change="+3 en progreso"
        icon="truck"
        color="green"
    />

    <x-stats.card
        title="Ingresos del Mes"
        :value="'$' . number_format($ingresosMes / 1000000, 1) . 'M'"
        change="+12% vs mes anterior"
        icon="currency-dollar"
        color="purple"
    />

    <x-stats.card
        title="En Mantenimiento"
        :value="$mediosMantenimiento"
        change="2 requieren atención"
        changeType="neutral"
        icon="wrench-screwdriver"
        color="amber"
    />
</x-dashboard.stats-grid>