<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'administrateur',
                'description' => 'Contrôle total, valide les projets et rôles'
            ],
            [
                'name' => 'secretariat',
                'description' => 'Gère les comptes artistes et affectations'
            ],
            [
                'name' => 'responsable_studio',
                'description' => 'Suit et met à jour les étapes de production'
            ],
            [
                'name' => 'artiste',
                'description' => 'Visualise ses projets, lecture seule'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
} 