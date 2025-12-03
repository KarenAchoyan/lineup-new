<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@lineup.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@lineup.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role to admin user
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole && !$admin->roles->contains($adminRole)) {
            $admin->roles()->attach($adminRole);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@lineup.com');
        $this->command->info('Password: password');
    }
}
