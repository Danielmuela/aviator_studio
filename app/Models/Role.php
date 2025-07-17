<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function getRoles()
    {
        return [
            'administrateur' => 'Contrôle total, valide les projets et rôles',
            'secretariat' => 'Gère les comptes artistes et affectations',
            'responsable_studio' => 'Suit et met à jour les étapes de production',
            'artiste' => 'Visualise ses projets, lecture seule'
        ];
    }
}