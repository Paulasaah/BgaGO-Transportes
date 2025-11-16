<?php

namespace Tests\Feature\Reservation;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Enums\ReservationStatus;
use App\Enums\VehicleStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

class CancelReservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'cliente']);

        // Crear usuarios
        $this->user = User::factory()->create();
        $this->user->assignRole('cliente');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Crear reserva
        $branch = Branch::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'sede_id' => $branch->id,
            'estado' => VehicleStatus::Ocupado,
        ]);

        $this->reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'vehiculo_id' => $vehicle->id,
            'sede_id' => $branch->id,
            'estado' => ReservationStatus::Pendiente,
        ]);
    }

    /** @test */
    public function usuario_puede_cancelar_su_propia_reserva_pendiente()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/reservations/{$this->reservation->id}/cancel", [
            'motivo_cancelacion' => 'Ya no necesito el vehículo',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'estado' => ReservationStatus::Cancelada->value,
            'cancelado_por' => $this->user->id,
        ]);
    }

    /** @test */
    public function admin_puede_cancelar_cualquier_reserva()
    {
        Sanctum::actingAs($this->admin);

        // Cambiar estado a activa (normalmente no cancelable por usuario)
        $this->reservation->update(['estado' => ReservationStatus::Activa]);

        $response = $this->postJson("/api/reservations/{$this->reservation->id}/cancel", [
            'motivo_cancelacion' => 'Cancelación administrativa',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'estado' => ReservationStatus::Cancelada->value,
            'cancelado_por' => $this->admin->id,
        ]);
    }

    /** @test */
    public function usuario_no_puede_cancelar_reserva_de_otro_usuario()
    {
        $otroUsuario = User::factory()->create();
        $otroUsuario->assignRole('cliente');
        
        Sanctum::actingAs($otroUsuario);

        $response = $this->postJson("/api/reservations/{$this->reservation->id}/cancel", [
            'motivo_cancelacion' => 'Intento de cancelar reserva ajena',
        ]);

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function cancelacion_libera_vehiculo()
    {
        Sanctum::actingAs($this->user);

        $vehicle = $this->reservation->vehicle;
        $this->assertEquals(VehicleStatus::Ocupado, $vehicle->estado);

        $this->postJson("/api/reservations/{$this->reservation->id}/cancel", [
            'motivo_cancelacion' => 'Test',
        ]);

        $vehicle->refresh();
        $this->assertEquals(VehicleStatus::Disponible, $vehicle->estado);
    }

    /** @test */
    public function usuario_no_puede_cancelar_reserva_completada()
    {
        $this->reservation->update(['estado' => ReservationStatus::Completada]);
        
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/reservations/{$this->reservation->id}/cancel", [
            'motivo_cancelacion' => 'Intento cancelar completada',
        ]);

        $response->assertStatus(422) // Validación - no se puede cancelar completada
            ->assertJsonValidationErrors(['estado']);
    }

    /** @test */
    public function cancelacion_registra_fecha_y_motivo()
    {
        Sanctum::actingAs($this->user);

        $motivo = 'Cambio de planes';
        
        $response = $this->postJson("/api/reservations/{$this->reservation->id}/cancel", [
            'motivo_cancelacion' => $motivo,
        ]);

        $response->assertStatus(200);

        $this->reservation->refresh();
        
        $this->assertEquals($motivo, $this->reservation->motivo_cancelacion);
        $this->assertNotNull($this->reservation->fecha_cancelacion);
        $this->assertEquals($this->user->id, $this->reservation->cancelado_por);
    }
}
