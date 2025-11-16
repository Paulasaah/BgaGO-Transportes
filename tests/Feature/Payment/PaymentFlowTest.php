<?php

namespace Tests\Feature\Payment;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Enums\ReservationStatus;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear permisos
        Permission::create(['name' => 'manage-payments']);
        
        // Crear usuario
        $this->user = User::factory()->create();

        // Crear reserva
        $branch = Branch::factory()->create();
        $vehicle = Vehicle::factory()->create(['sede_id' => $branch->id]);

        $this->reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'vehiculo_id' => $vehicle->id,
            'sede_id' => $branch->id,
            'estado' => ReservationStatus::Pendiente,
            'monto_final' => 150.00,
        ]);
    }

    /** @test */
    public function usuario_puede_crear_intencion_de_pago()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/payments/reservations/{$this->reservation->id}/create-intent", [
            'metodo_pago' => 'mercadopago',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'payment_id',
                    'amount',
                ]
            ]);
    }

    /** @test */
    public function pago_aprobado_confirma_reserva()
    {
        Sanctum::actingAs($this->user);

        // Crear pago
        $payment = Payment::create([
            'reserva_id' => $this->reservation->id,
            'user_id' => $this->user->id,
            'monto' => $this->reservation->monto_final,
            'metodo_pago' => 'mercadopago',
            'estado' => PaymentStatus::Pendiente,
        ]);

        // Simular aprobación de pago (normalmente vendría de webhook)
        $payment->update(['estado' => PaymentStatus::Aprobado]);

        // Confirmar reserva
        $response = $this->postJson("/api/reservations/{$this->reservation->id}/confirm");

        $response->assertStatus(200);

        $this->reservation->refresh();
        $this->assertEquals(ReservationStatus::Confirmada, $this->reservation->estado);
        $this->assertNotNull($this->reservation->fecha_confirmacion);
    }

    /** @test */
    public function no_puede_confirmar_reserva_sin_pago_aprobado()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/reservations/{$this->reservation->id}/confirm");

        $response->assertStatus(500); // Error del servicio
    }

    /** @test */
    public function rate_limiting_en_pagos()
    {
        Sanctum::actingAs($this->user);

        // Intentar crear 6 intenciones de pago (límite es 5/min)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson("/api/payments/reservations/{$this->reservation->id}/create-intent", [
                'metodo_pago' => 'mercadopago',
            ]);

            if ($i < 5) {
                $response->assertStatus(201);
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    /** @test */
    public function admin_puede_aprobar_pago_manualmente()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('manage-payments');
        
        Sanctum::actingAs($admin);

        $payment = Payment::create([
            'reserva_id' => $this->reservation->id,
            'user_id' => $this->user->id,
            'monto' => $this->reservation->monto_final,
            'metodo_pago' => 'efectivo',
            'estado' => PaymentStatus::Pendiente,
        ]);

        $response = $this->postJson("/api/payments/{$payment->id}/approve");

        $response->assertStatus(200);

        $payment->refresh();
        $this->assertEquals(PaymentStatus::Aprobado, $payment->estado);
    }

    /** @test */
    public function admin_puede_rechazar_pago()
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('manage-payments');
        
        Sanctum::actingAs($admin);

        $payment = Payment::create([
            'reserva_id' => $this->reservation->id,
            'user_id' => $this->user->id,
            'monto' => $this->reservation->monto_final,
            'metodo_pago' => 'transferencia',
            'estado' => PaymentStatus::Pendiente,
        ]);

        $response = $this->postJson("/api/payments/{$payment->id}/reject", [
            'motivo' => 'Fondos insuficientes',
        ]);

        $response->assertStatus(200);

        $payment->refresh();
        $this->assertEquals(PaymentStatus::Rechazado, $payment->estado);
    }

    /** @test */
    public function usuario_puede_ver_sus_propios_pagos()
    {
        Sanctum::actingAs($this->user);

        // Crear algunos pagos
        Payment::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'reserva_id' => $this->reservation->id,
        ]);

        $response = $this->getJson('/api/payments/me/list');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
