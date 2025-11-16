<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ReservationService;
use App\Services\PricingService;
use App\Services\VehicleAvailabilityService;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Enums\VehicleStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReservationService $service;
    protected User $user;
    protected Vehicle $vehicle;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->branch = Branch::factory()->create();
        $this->vehicle = Vehicle::factory()->create([
            'sede_id' => $this->branch->id,
            'estado' => VehicleStatus::Disponible,
        ]);

        // Mock de servicios dependientes
        $pricingService = Mockery::mock(PricingService::class);
        $pricingService->shouldReceive('calculateReservationPrice')
            ->andReturn([
                'success' => true,
                'data' => [
                    'subtotal' => 100,
                    'descuento' => 0,
                    'total' => 100,
                ]
            ]);

        $availabilityService = Mockery::mock(VehicleAvailabilityService::class);
        $availabilityService->shouldReceive('checkAvailability')
            ->andReturn(['success' => true]);

        $this->service = new ReservationService($pricingService, $availabilityService);
    }

    /** @test */
    public function puede_crear_reserva_con_datos_validos()
    {
        $data = [
            'user_id' => $this->user->id,
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'fecha_inicio' => now()->addDay()->toDateTimeString(),
            'fecha_fin' => now()->addDay()->addHours(2)->toDateTimeString(),
            'origen_direccion' => 'Calle 123',
            'destino_direccion' => 'Calle 456',
        ];

        $result = $this->service->createReservation($data);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Reservation::class, $result['data']);
        $this->assertEquals(ReservationStatus::Pendiente, $result['data']->estado);
    }

    /** @test */
    public function falla_si_faltan_campos_requeridos()
    {
        $data = [
            'user_id' => $this->user->id,
            // Falta vehiculo_id
            'sede_id' => $this->branch->id,
        ];

        $result = $this->service->createReservation($data);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('requerido', strtolower($result['message']));
    }

    /** @test */
    public function actualiza_estado_vehiculo_a_ocupado()
    {
        $data = [
            'user_id' => $this->user->id,
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'fecha_inicio' => now()->addDay()->toDateTimeString(),
            'fecha_fin' => now()->addDay()->addHours(2)->toDateTimeString(),
            'origen_direccion' => 'Calle 123',
        ];

        $this->service->createReservation($data);

        $this->vehicle->refresh();
        $this->assertEquals(VehicleStatus::Ocupado, $this->vehicle->estado);
    }

    /** @test */
    public function puede_confirmar_reserva_pendiente_con_pago()
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'estado' => ReservationStatus::Pendiente,
        ]);

        // Simular pago aprobado
        $reservation->payments()->create([
            'user_id' => $this->user->id,
            'monto' => 100,
            'metodo_pago' => 'mercadopago',
            'estado' => 'aprobado',
        ]);

        $result = $this->service->confirmReservation($reservation->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(ReservationStatus::Confirmada, $result['data']->estado);
        $this->assertNotNull($result['data']->fecha_confirmacion);
    }

    /** @test */
    public function no_puede_confirmar_sin_pago_aprobado()
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'estado' => ReservationStatus::Pendiente,
        ]);

        $result = $this->service->confirmReservation($reservation->id);

        $this->assertFalse($result['success']);
    }

    /** @test */
    public function puede_iniciar_reserva_confirmada()
    {
        $reservation = Reservation::factory()->create([
            'estado' => ReservationStatus::Confirmada,
        ]);

        $result = $this->service->startReservation($reservation->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(ReservationStatus::Activa, $result['data']->estado);
        $this->assertNotNull($result['data']->fecha_inicio_real);
    }

    /** @test */
    public function puede_completar_reserva_activa()
    {
        $reservation = Reservation::factory()->create([
            'vehiculo_id' => $this->vehicle->id,
            'estado' => ReservationStatus::Activa,
        ]);

        $result = $this->service->completeReservation($reservation->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(ReservationStatus::Completada, $result['data']->estado);
        $this->assertNotNull($result['data']->fecha_fin_real);

        // Verificar que libera el vehículo
        $this->vehicle->refresh();
        $this->assertEquals(VehicleStatus::Disponible, $this->vehicle->estado);
    }

    /** @test */
    public function puede_cancelar_reserva_pendiente()
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'vehiculo_id' => $this->vehicle->id,
            'estado' => ReservationStatus::Pendiente,
        ]);

        $motivo = 'Ya no necesito el vehículo';
        $result = $this->service->cancelReservation($reservation->id, $motivo, $this->user->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(ReservationStatus::Cancelada, $result['data']->estado);
        $this->assertEquals($motivo, $result['data']->motivo_cancelacion);
    }

    /** @test */
    public function obtiene_reservas_activas_del_usuario()
    {
        // Crear reservas activas
        Reservation::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'estado' => ReservationStatus::Confirmada,
        ]);

        // Crear reserva completada (no debe aparecer)
        Reservation::factory()->create([
            'user_id' => $this->user->id,
            'estado' => ReservationStatus::Completada,
        ]);

        $result = $this->service->getUserActiveReservations($this->user->id);

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']);
    }

    /** @test */
    public function calcula_estadisticas_del_usuario()
    {
        // Crear diferentes tipos de reservas
        Reservation::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'estado' => ReservationStatus::Completada,
            'monto_final' => 100,
        ]);

        Reservation::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'estado' => ReservationStatus::Cancelada,
        ]);

        $result = $this->service->getUserStats($this->user->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(5, $result['data']['total']);
        $this->assertEquals(3, $result['data']['completadas']);
        $this->assertEquals(2, $result['data']['canceladas']);
        $this->assertEquals(300, $result['data']['gasto_total']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
