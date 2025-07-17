<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjetEtape extends Model
{
    use HasFactory;

    protected $fillable = ['projet_id', 'etape', 'modifie_par', 'commentaire'];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function modifiePar()
    {
        return $this->belongsTo(User::class, 'modifie_par');
    }

    public function getEtapeLabelAttribute()
    {
        return [
            'enregistrement' => 'Enregistrement',
            'mixage' => 'Mixage',
            'mastering' => 'Mastering',
            'distribution_en_cours' => 'Distribution en cours',
            'distribution_terminee' => 'Distribution terminée'
        ][$this->etape] ?? 'Inconnu';
    }

    public function getEtapeColorAttribute()
    {
        return [
            'enregistrement' => 'blue',
            'mixage' => 'pink',
            'mastering' => 'yellow',
            'distribution_en_cours' => 'orange',
            'distribution_terminee' => 'green'
        ][$this->etape] ?? 'gray';
    }

    public function getEtapeColorClassAttribute()
    {
        $colors = [
            'enregistrement' => 'bg-blue-100 text-blue-800',
            'mixage' => 'bg-pink-100 text-pink-800',
            'mastering' => 'bg-yellow-100 text-yellow-800',
            'distribution_en_cours' => 'bg-orange-100 text-orange-800',
            'distribution_terminee' => 'bg-green-100 text-green-800'
        ];
        
        return $colors[$this->etape] ?? 'bg-gray-100 text-gray-800';
    }
} 