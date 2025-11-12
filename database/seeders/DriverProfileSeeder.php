<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DriverProfile;
use App\Models\User;

class DriverProfileSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(10)->get();

        $licenses = [
            'DRV-45821', 'DRV-71245', 'DRV-39812', 'DRV-56214', 'DRV-24587',
            'DRV-91245', 'DRV-78541', 'DRV-36412', 'DRV-65412', 'DRV-95123',
        ];

        foreach ($users as $i => $user) {
            DriverProfile::updateOrCreate(
                ['user_id' => $user->id], // clave única
                [
                    'license_number' => $licenses[$i] ?? 'DRV-99999',
                    'rating' => rand(4, 5),
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ Perfiles de conductores creados o actualizados correctamente.');
    }
}
