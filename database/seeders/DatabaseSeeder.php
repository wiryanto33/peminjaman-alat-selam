<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $admin = User::factory()->create([
            'name' => 'admin',
            'pangkat' => 'sertu',
            'nrp' => '123456',
            'jabatan' => 'admin',
            'email' => 'admin@admin.com',
        ]);

        // Ensure super_admin role exists and assign it to the seeded admin
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $admin->assignRole($superAdminRole);


        //call BookSeeder
        $this->call(
            [
                ShieldSeeder::class,
                BookSeeder::class,
                PostSeeder::class,
                ContactSeeder::class,
            ]
        );
    }
}
