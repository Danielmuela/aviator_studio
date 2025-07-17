<?php

namespace App\Policies;

use App\Models\Projet;
use App\Models\User;

class ProjetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isValide();
    }

    public function view(User $user, Projet $projet): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isResponsableStudio() || $user->isSecretariat()) {
            return true;
        }
        // Artiste peut voir seulement ses projets
        return $user->isArtiste() && $projet->artiste_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isResponsableStudio() || $user->isSecretariat();
    }

    public function update(User $user, Projet $projet): bool
    {
        return $user->isAdmin() || $user->isResponsableStudio();
    }

    public function delete(User $user, Projet $projet): bool
    {
        return $user->isAdmin();
    }
} 