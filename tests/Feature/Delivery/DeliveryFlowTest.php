<?php

namespace Tests\Feature\Delivery;

use Tests\TestCase;
use App\Models\User;
use App\Models\Delivery;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Enums\ReservationType;
use App\Enums\ReservationStatus;
use App\Enums\DeliveryStatus;
use App\Enums\DeliveryType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

class DeliveryFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $cliente;
    protected User $conductor;
    protected User $admin;
    protected Branch $branch;
    protected Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles
        Role::create(['name' => 'cliente']);
        Role::create(['name' => 'conductor']);
        Role::create(['name' => 'admin']);

        // Crear usuarios
        $this->cliente = User::factory()->create();
        $this->cliente->assignRole('cliente');

        $this->conductor = User::factory()->create();
        $this->conductor->assignRole('conductor');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Crear datos base
        $this->branch = Branch::factory()->create();
        $this->vehicle = Vehicle::factory()->create(['sede_id' => $this->branch->id]);
    }

    /** @test */
    public function cliente_puede_crear_domicilio_de_paquete()
    {
        Sanctum::actingAs($this->cliente);

        $response = $this->postJson('/api/deliveries/package', [
            'origen_direccion' => 'Calle 1 #123',
            'destino_direccion' => 'Calle 2 #456',
            'descripcion_paquete' => 'Documentos importantes',
            'peso_kg' => 2.5,
            'notas_cliente' => 'Manejar con cuidado',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'tipo',
                    'estado',
                ]
            ]);

        $this->assertDatabaseHas('deliveries', [
            'tipo' => DeliveryType::Paquete->value,
            'estado' => DeliveryStatus::Pendiente->value,
        ]);
    }

    /** @test */
    public function cliente_puede_crear_domicilio_de_vehiculo()
    {
        Sanctum::actingAs($this->cliente);

        $response = $this->postJson('/api/deliveries/vehicle', [
            'vehiculo_id' => $this->vehicle->id,
            'origen_direccion' => 'Sede Principal',
            'destino_direccion' => 'Cliente Calle 10',
            'fecha_entrega' => now()->addDay()->toIso8601String(),
            'notas_cliente' => 'Entrega urgente',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('deliveries', [
            'tipo' => DeliveryType::Vehiculo->value,
            'estado' => DeliveryStatus::Pendiente->value,
        ]);
    }

    /** @test */
    public function admin_puede_asignar_conductor_a_domicilio()
    {
        Sanctum::actingAs($this->admin);

        // Crear domicilio
        $reservation = Reservation::factory()->create([
            'user_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'tipo' => ReservationType::Domicilio,
            'estado' => ReservationStatus::Pendiente,
        ]);

        $delivery = Delivery::factory()->create([
            'reserva_id' => $reservation->id,
            'estado' => DeliveryStatus::Pendiente,
        ]);

        $response = $this->postJson("/api/deliveries/{$delivery->id}/assign-driver", [
            'conductor_id' => $this->conductor->id,
        ]);

        $response->assertStatus(200);

        $reservation->refresh();
        $this->assertEquals($this->conductor->id, $reservation->conductor_id);
    }

    /** @test */
    public function conductor_puede_iniciar_domicilio_asignado()
    {
        Sanctum::actingAs($this->conductor);

        $reservation = Reservation::factory()->create([
            'user_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'conductor_id' => $this->conductor->id,
            'tipo' => ReservationType::Domicilio,
            'estado' => ReservationStatus::Confirmada,
        ]);

        $delivery = Delivery::factory()->create([
            'reserva_id' => $reservation->id,
            'estado' => DeliveryStatus::Asignado,
        ]);

        $response = $this->postJson("/api/deliveries/{$delivery->id}/start");

        $response->assertStatus(200);

        $delivery->refresh();
        $this->assertEquals(DeliveryStatus::EnCurso, $delivery->estado);
        $this->assertNotNull($delivery->fecha_inicio_real);
    }

    /** @test */
    public function conductor_puede_completar_domicilio()
    {
        Sanctum::actingAs($this->conductor);

        $reservation = Reservation::factory()->create([
            'user_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'conductor_id' => $this->conductor->id,
            'tipo' => ReservationType::Domicilio,
            'estado' => ReservationStatus::Activa,
        ]);

        $delivery = Delivery::factory()->create([
            'reserva_id' => $reservation->id,
            'estado' => DeliveryStatus::EnCurso,
            'fecha_inicio_real' => now()->subHour(),
        ]);

        $response = $this->postJson("/api/deliveries/{$delivery->id}/complete", [
            'firma_receptor' => 'Juan Pérez',
            'foto_entrega' => 'base64_encoded_image',
            'notas_entrega' => 'Entregado exitosamente',
        ]);

        $response->assertStatus(200);

        $delivery->refresh();
        $this->assertEquals(DeliveryStatus::Entregado, $delivery->estado);
        $this->assertNotNull($delivery->fecha_entrega_real);
        $this->assertEquals('Juan Pérez', $delivery->firma_receptor);
    }

    /** @test */
    public function cliente_puede_rastrear_su_domicilio()
    {
        Sanctum::actingAs($this->cliente);

        $reservation = Reservation::factory()->create([
            'user_id' => $this->cliente->id,
            'vehiculo_id' => $this->vehicle->id,
            'sede_id' => $this->branch->id,
            'tipo' => ReservationType::Domicilio,
        ]);

        $delivery = Delivery::factory()->create([
            'reserva_id' => $reservation->id,
            'estado' => DeliveryStatus::EnCurso,
        ]);

        $response = $this->getJson("/api/deliveries/{$delivery->id}/track");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'estado',
                    'ubicacion_actual',
                    'tiempo_estimado',
                ]
            ]);
    }

    /** @test */
    public function conductor_puede_ver_sus_domicilios_asignados()
    {
        Sanctum::actingAs($this->conductor);

        // Crear varios domicilios asignados
        for ($i = 0; $i < 3; $i++) {
            $reservation = Reservation::factory()->create([
                'user_id' => $this->cliente->id,
                'vehiculo_id' => $this->vehicle->id,
                'sede_id' => $this->branch->id,
                'conductor_id' => $this->conductor->id,
                'tipo' => ReservationType::Domicilio,
            ]);

            Delivery::factory()->create([
                'reserva_id' => $reservation->id,
                'estado' => DeliveryStatus::Asignado,
            ]);
        }

        $response = $this->getJson('/api/deliveries/me/assigned');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function admin_puede_ver_domicilios_pendientes()
    {
        Sanctum::actingAs($this->admin);

        // Crear domicilios pendientes
        for ($i = 0; $i < 5; $i++) {
            $reservation = Reservation::factory()->create([
                'user_id' => $this->cliente->id,
                'vehiculo_id' => $this->vehicle->id,
                'sede_id' => $this->branch->id,
                'tipo' => ReservationType::Domicilio,
                'estado' => ReservationStatus::Pendiente,
            ]);

            Delivery::factory()->create([
                'reserva_id' => $reservation->id,
                'estado' => DeliveryStatus::Pendiente,
            ]);
        }

        $response = $this->getJson('/api/deliveries/pending/list');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }
}
