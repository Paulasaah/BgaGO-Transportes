<?php

namespace Tests\Feature\Reservation;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Enums\VehicleStatus;
use App\Enums\ReservationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class CreateReservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Vehicle $vehicle;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos de prueba
        $this->user = User::factory()->create();
        $this->branch = Branch::factory()->create();
        $this->vehicle = Vehicle::factory()->create([
            'sede_id' => $this->branch->id,
            'estado' => VehicleStatus::Disponible,
        ]);
    }

    /** @test */
    public function usuario_autenticado_puede_crear_reserva()
    {
        Sanctum::actingAs($this->user);

        $fechaInicio = now()->addDay();
        $fechaFin = $fechaInicio->copy()->addHours(2);

        $response = $this->postJson('/api/reservations', [
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'fecha_inicio' => $fechaInicio->toIso8601String(),
            'fecha_fin' => $fechaFin->toIso8601String(),
            'origen_direccion' => 'Calle 123',
            'destino_direccion' => 'Calle 456',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'codigo',
                    'estado',
                    'monto_final',
                ]
            ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'vehiculo_id' => $this->vehicle->id,
            'estado' => ReservationStatus::Pendiente->value,
        ]);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_crear_reserva()
    {
        $response = $this->postJson('/api/reservations', [
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'fecha_inicio' => now()->addDay()->toIso8601String(),
            'fecha_fin' => now()->addDay()->addHours(2)->toIso8601String(),
            'origen_direccion' => 'Calle 123',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function validacion_fecha_inicio_debe_ser_futura()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/reservations', [
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'fecha_inicio' => now()->subHour()->toIso8601String(), // Fecha pasada
            'fecha_fin' => now()->addHour()->toIso8601String(),
            'origen_direccion' => 'Calle 123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_inicio']);
    }

    /** @test */
    public function validacion_duracion_minima_una_hora()
    {
        Sanctum::actingAs($this->user);

        $fechaInicio = now()->addDay();
        $fechaFin = $fechaInicio->copy()->addMinutes(30); // Solo 30 minutos

        $response = $this->postJson('/api/reservations', [
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'fecha_inicio' => $fechaInicio->toIso8601String(),
            'fecha_fin' => $fechaFin->toIso8601String(),
            'origen_direccion' => 'Calle 123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_fin']);
    }

    /** @test */
    public function no_puede_crear_mas_de_3_reservas_activas()
    {
        Sanctum::actingAs($this->user);

        // Crear 3 reservas activas
        for ($i = 0; $i < 3; $i++) {
            $this->user->reservations()->create([
                'codigo' => 'RES-2024-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'vehiculo_id' => $this->vehicle->id,
                'sede_id' => $this->branch->id,
                'tipo' => 'reserva',
                'estado' => ReservationStatus::Pendiente,
                'fecha_inicio' => now()->addDays($i + 1),
                'fecha_fin' => now()->addDays($i + 1)->addHours(2),
                'monto' => 100,
                'monto_final' => 100,
                'origen_direccion' => 'Origen',
                'destino_direccion' => 'Destino',
            ]);
        }

        // Intentar crear la cuarta
        $response = $this->postJson('/api/reservations', [
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'fecha_inicio' => now()->addDays(10)->toIso8601String(),
            'fecha_fin' => now()->addDays(10)->addHours(2)->toIso8601String(),
            'origen_direccion' => 'Calle 123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user']);
    }

    /** @test */
    public function reserva_genera_codigo_unico()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/reservations', [
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'fecha_inicio' => now()->addDay()->toIso8601String(),
            'fecha_fin' => now()->addDay()->addHours(2)->toIso8601String(),
            'origen_direccion' => 'Calle 123',
        ]);

        $response->assertStatus(201);
        
        $codigo = $response->json('data.codigo');
        $this->assertMatchesRegularExpression('/^RES-\d{4}-\d{4}$/', $codigo);
    }
}
