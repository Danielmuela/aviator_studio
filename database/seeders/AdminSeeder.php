<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'administrateur')->first();
        
        if ($adminRole) {
            User::create([
                'name' => 'Administrateur',
                'email' => 'admin@aviatrax-studio.com',
                'telephone' => '0123456789',
                'password' => Hash::make('password'),
                'statut' => 'valide',
                'role_id' => $adminRole->id,
            ]);
        }
    }
} 