<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'telephone', 'photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relation avec le rôle.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Projets où l'utilisateur est l'artiste.
     */
    public function projetsArtiste()
    {
        return $this->hasMany(Projet::class, 'artiste_id');
    }

    /**
     * Projets où l'utilisateur est le responsable studio.
     */
    public function projetsResponsable()
    {
        return $this->hasMany(Projet::class, 'responsable_id');
    }

    /**
     * Responsable studio de l'artiste.
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Artistes affectés à ce responsable studio.
     */
    public function artistesAffectes()
    {
        return $this->hasMany(User::class, 'responsable_id');
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique.
     */
    public function hasRole($roleName)
    {
        return $this->role && strtolower($this->role->name) === strtolower($roleName);
    }

    public function isAdmin()
    {
        return $this->hasRole('administrateur');
    }

    public function isSecretariat()
    {
        return $this->hasRole('secretariat');
    }

    public function isResponsableStudio()
    {
        return $this->hasRole('responsable'); // Corrigé pour correspondre au nom du rôle en base
    }

    public function isArtiste()
    {
        return $this->hasRole('artiste');
    }

    public function isSecretaire()
    {
        return $this->role && $this->role->name === 'secretaire';
    }

    /**
     * Vérifie si le compte est validé.
     */
    public function isValide()
    {
        return $this->statut === 'valide';
    }
}