<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::factory()->create([
            'name' => 'Filament Admin',
            'email' => 'admin@filamentphp.com',
            'phone_number' => fake()->phoneNumber(),
            'active' => true,
            'is_admin' => true
        ]);

        $users = User::factory(49)->create();
    }
}
