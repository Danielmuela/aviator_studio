<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\Projet;
use App\Policies\ProjetPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Résoudre le problème de clé trop longue avec MySQL
        Schema::defaultStringLength(191);
        
        Gate::policy(Projet::class, ProjetPolicy::class);
        
        // Gate pour l'accès admin
        Gate::define('admin-access', function ($user) {
            return $user->role && $user->role->name === 'administrateur';
        });

        Gate::define('secretariat-access', function ($user) {
            return $user->role && $user->role->name === 'secretariat';
        });

        Gate::define('studio-access', function ($user) {
            return $user->role && $user->role->name === 'responsable_studio';
        });

        Gate::define('artiste-access', function ($user) {
            return $user->role && $user->role->name === 'artiste';
        });
    }
}
