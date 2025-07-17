<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class SecretariatController extends Controller
{
    public function dashboard()
    {
        Gate::authorize('secretariat-access');
        
        $artistes = User::whereHas('role', function($query) {
            $query->where('name', 'artiste');
        })->get();

        $stats = [
            'total_artistes' => $artistes->count(),
            'artistes_actifs' => $artistes->where('statut', 'valide')->count(),
            'artistes_en_attente' => $artistes->where('statut', 'en_attente_validation')->count(),
        ];

        return view('secretariat.dashboard', compact('artistes', 'stats'));
    }

    public function createArtiste()
    {
        Gate::authorize('secretariat-access');
        
        return view('secretariat.create-artiste');
    }

    public function storeArtiste(Request $request)
    {
        Gate::authorize('secretariat-access');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Récupérer le rôle artiste
        $roleArtiste = Role::where('name', 'artiste')->first();
        
        if (!$roleArtiste) {
            return redirect()->back()->with('error', 'Rôle artiste non trouvé.');
        }

        $artiste = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'role_id' => $roleArtiste->id,
            'statut' => 'valide', // Le secrétariat peut valider directement
        ]);

        Log::info('Compte artiste créé par le secrétariat', [
            'artiste_id' => $artiste->id,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('secretariat.dashboard')->with('success', 'Compte artiste créé avec succès.');
    }

    public function editArtiste(User $artiste)
    {
        Gate::authorize('secretariat-access');
        
        // Vérifier que c'est bien un artiste
        if (!$artiste->role || $artiste->role->name !== 'artiste') {
            return redirect()->back()->with('error', 'Utilisateur non trouvé.');
        }

        return view('secretariat.edit-artiste', compact('artiste'));
    }

    public function updateArtiste(Request $request, User $artiste)
    {
        Gate::authorize('secretariat-access');
        
        // Vérifier que c'est bien un artiste
        if (!$artiste->role || $artiste->role->name !== 'artiste') {
            return redirect()->back()->with('error', 'Utilisateur non trouvé.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $artiste->id,
            'telephone' => 'required|string|max:20',
            'statut' => 'required|in:valide,suspendu',
        ]);

        $artiste->update([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'statut' => $request->statut,
        ]);

        Log::info('Artiste modifié par le secrétariat', [
            'artiste_id' => $artiste->id,
            'modified_by' => Auth::id()
        ]);

        return redirect()->route('secretariat.dashboard')->with('success', 'Artiste mis à jour avec succès.');
    }

    public function suspendArtiste(User $artiste)
    {
        Gate::authorize('secretariat-access');
        
        // Vérifier que c'est bien un artiste
        if (!$artiste->role || $artiste->role->name !== 'artiste') {
            return redirect()->back()->with('error', 'Utilisateur non trouvé.');
        }

        $artiste->update(['statut' => 'suspendu']);

        Log::info('Artiste suspendu par le secrétariat', [
            'artiste_id' => $artiste->id,
            'suspended_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Artiste suspendu avec succès.');
    }

    public function activateArtiste(User $artiste)
    {
        Gate::authorize('secretariat-access');
        
        // Vérifier que c'est bien un artiste
        if (!$artiste->role || $artiste->role->name !== 'artiste') {
            return redirect()->back()->with('error', 'Utilisateur non trouvé.');
        }

        $artiste->update(['statut' => 'valide']);

        Log::info('Artiste réactivé par le secrétariat', [
            'artiste_id' => $artiste->id,
            'activated_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Artiste réactivé avec succès.');
    }

    public function affecterResponsable(Request $request, User $artiste)
    {
        Gate::authorize('secretariat-access');
        // Autoriser aussi l'admin
        if (!Auth::user()->isSecretariat() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        // Vérifier que c'est bien un artiste validé
        if (!$artiste->role || $artiste->role->name !== 'artiste' || $artiste->statut !== 'valide') {
            return redirect()->back()->with('error', 'Seuls les artistes validés peuvent être affectés.');
        }
        $request->validate([
            'responsable_id' => 'required|exists:users,id',
        ]);
        $responsable = User::where('id', $request->responsable_id)->whereHas('role', function($q) {
            $q->where('name', 'responsable_studio');
        })->first();
        if (!$responsable) {
            return redirect()->back()->with('error', 'Responsable studio invalide.');
        }
        $artiste->responsable_id = $responsable->id;
        $artiste->save();
        // Notification Laravel
        $responsable->notify(new \App\Notifications\ArtisteAffecteNotification($artiste));
        return redirect()->back()->with('success', 'Artiste affecté à ' . $responsable->name . ' avec succès.');
    }
} 