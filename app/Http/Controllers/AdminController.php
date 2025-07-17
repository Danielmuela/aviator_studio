<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function dashboard()
    {
        Gate::authorize('admin-access');
        
        $stats = [
            'total_users' => User::count(),
            'users_en_attente' => User::where('statut', 'en_attente_validation')->count(),
            'total_projets' => Projet::count(),
            'projets_actifs' => Projet::where('statut', 'actif')->count(),
        ];

        $usersEnAttente = User::where('statut', 'en_attente_validation')->get();
        $projetsRecents = Projet::with(['artiste', 'responsable'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'usersEnAttente', 'projetsRecents'));
    }

    public function users()
    {
        Gate::authorize('admin-access');
        
        $users = User::with('role')->get();
        $roles = Role::all();

        return view('admin.users', compact('users', 'roles'));
    }

    public function validateUser(User $user)
    {
        Gate::authorize('admin-access');
        
        $user->update(['statut' => 'valide']);

        Log::info('Utilisateur validé', [
            'user_id' => $user->id,
            'validated_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Utilisateur validé avec succès.');
    }

    public function updateUserRole(Request $request, User $user)
    {
        Gate::authorize('admin-access');
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $adminRole = Role::where('name', 'administrateur')->first();
        $currentUser = Auth::user();

        // Empêcher qu'un non-admin attribue le rôle administrateur
        if ($request->role_id == $adminRole->id && !$currentUser->isAdmin()) {
            return redirect()->back()->with('error', 'Seul un administrateur peut attribuer le rôle Administrateur.');
        }
        // Empêcher qu'un admin se retire lui-même le rôle admin
        if ($user->id == $currentUser->id && $request->role_id != $adminRole->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas retirer votre propre rôle Administrateur.');
        }

        // Récupérer l'ancien rôle de manière sécurisée
        $ancienRole = $user->role && $user->role->name ? $user->role->name : 'Aucun';
        $user->update(['role_id' => $request->role_id]);
        // Récupérer le nouveau rôle de manière sécurisée
        $nouveauRole = $user->fresh()->role && $user->fresh()->role->name ? $user->fresh()->role->name : 'Aucun';

        Log::info('Rôle utilisateur modifié', [
            'user_id' => $user->id,
            'ancien_role' => $ancienRole,
            'nouveau_role' => $nouveauRole,
            'modified_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Rôle mis à jour avec succès.');
    }

    public function suspendUser(User $user)
    {
        Gate::authorize('admin-access');
        
        $user->update(['statut' => 'suspendu']);

        Log::info('Utilisateur suspendu', [
            'user_id' => $user->id,
            'suspended_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Utilisateur suspendu avec succès.');
    }

    public function activateUser(User $user)
    {
        Gate::authorize('admin-access');
        
        $user->update(['statut' => 'valide']);

        Log::info('Utilisateur réactivé', [
            'user_id' => $user->id,
            'activated_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Utilisateur réactivé avec succès.');
    }

    public function logs()
    {
        Gate::authorize('admin-access');
        
        // Lire les logs Laravel
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logFile)) {
            $logs = file($logFile);
            $logs = array_slice($logs, -100); // Dernières 100 lignes
        }

        return view('admin.logs', compact('logs'));
    }
} 