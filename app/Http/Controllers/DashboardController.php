<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name;

        return match ($role) {
            'administrateur' => $this->adminDashboard(),
            'secretariat' => $this->secretariatDashboard(),
            'responsable' => $this->responsableDashboard(),
            'artiste' => $this->artisteDashboard(),
            default => abort(403),
        };
    }

    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'users_en_attente' => User::where('statut', 'en_attente_validation')->count(),
            'total_projets' => Projet::count(),
            'projets_actifs' => Projet::where('statut', 'actif')->count(),
        ];

        $usersEnAttente = User::where('statut', 'en_attente_validation')->get();
        $projetsRecents = Projet::with(['artiste', 'responsable'])->latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'usersEnAttente', 'projetsRecents'));
    }

    private function secretariatDashboard()
    {
        $artistes = User::whereHas('role', function ($query) {
            $query->where('name', 'artiste');
        })->with('projetsArtiste')->get();

        $projets = Projet::with(['artiste', 'responsable'])->get();

        return view('dashboard.secretariat', compact('artistes', 'projets'));
    }

    private function responsableDashboard()
    {
        $projets = Projet::with(['artiste', 'responsable'])->get();
        
        $projetsParEtape = [
            'enregistrement' => $projets->where('etape', 'enregistrement')->count(),
            'mixage' => $projets->where('etape', 'mixage')->count(),
            'mastering' => $projets->where('etape', 'mastering')->count(),
            'distribution_en_cours' => $projets->where('etape', 'distribution_en_cours')->count(),
            'distribution_terminee' => $projets->where('etape', 'distribution_terminee')->count(),
        ];

        return view('dashboard.responsable', compact('projets', 'projetsParEtape'));
    }

    private function artisteDashboard()
    {
        $user = Auth::user();
        $projets = Projet::where('artiste_id', $user->id)->with(['responsable'])->get();
        
        $projetsParEtape = [
            'enregistrement' => $projets->where('etape', 'enregistrement')->count(),
            'mixage' => $projets->where('etape', 'mixage')->count(),
            'mastering' => $projets->where('etape', 'mastering')->count(),
            'distribution_en_cours' => $projets->where('etape', 'distribution_en_cours')->count(),
            'distribution_terminee' => $projets->where('etape', 'distribution_terminee')->count(),
        ];

        return view('dashboard.artiste', compact('projets', 'projetsParEtape'));
    }

    // Nouvelle méthode responsable
    public function responsable()
    {
        $projets = Projet::with('artiste')->get(); // responsable_id possible ici
        return view('dashboard.responsable', compact('projets'));
    }
}