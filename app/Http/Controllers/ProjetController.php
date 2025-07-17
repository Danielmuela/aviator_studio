<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class ProjetController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $projets = Projet::with(['artiste', 'responsable'])->get();
        } elseif ($user->isResponsableStudio()) {
            $projets = Projet::with(['artiste', 'responsable'])->get();
        } elseif ($user->isSecretariat()) {
            $projets = Projet::with(['artiste', 'responsable'])->get();
        } else {
            // Artiste - voir seulement ses projets
            $projets = $user->projetsArtiste()->with(['responsable'])->get();
        }

        return view('projets.index', compact('projets'));
    }

    public function create()
    {
        Gate::authorize('create', Projet::class);
        
        $artistes = User::whereHas('role', function($query) {
            $query->where('name', 'artiste');
        })->where('statut', 'valide')->get();
        
        $responsables = User::whereHas('role', function($query) {
            $query->where('name', 'responsable');
        })->where('statut', 'valide')->get();

        return view('projets.create', compact('artistes', 'responsables'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Projet::class);
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'artiste_id' => 'required|exists:users,id',
            'responsable_id' => 'required|exists:users,id',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut',
        ]);

        $projet = Projet::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'artiste_id' => $request->artiste_id,
            'responsable_id' => $request->responsable_id,
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin_prevue,
        ]);

        // Notifier l'artiste de la création du projet
        if ($projet->artiste) {
            $projet->artiste->notify(new \App\Notifications\ProjetEtapeUpdatedNotification($projet));
        }

        Log::info('Nouveau projet créé', [
            'projet_id' => $projet->id,
            'titre' => $projet->titre,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('projets.index')->with('success', 'Projet créé avec succès.');
    }

    public function show(Projet $projet)
    {
        Gate::authorize('view', $projet);
        
        $projet->load(['artiste', 'responsable', 'etapes.modifiePar']);
        
        return view('projets.show', compact('projet'));
    }

    public function edit(Projet $projet)
    {
        Gate::authorize('update', $projet);
        
        $artistes = User::whereHas('role', function($query) {
            $query->where('name', 'artiste');
        })->where('statut', 'valide')->get();
        
        $responsables = User::whereHas('role', function($query) {
            $query->where('name', 'responsable');
        })->where('statut', 'valide')->get();

        return view('projets.edit', compact('projet', 'artistes', 'responsables'));
    }

    public function update(Request $request, Projet $projet)
    {
        Gate::authorize('update', $projet);
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'artiste_id' => 'required|exists:users,id',
            'etape' => 'required|in:enregistrement,mixage,mastering,distribution_en_cours,distribution_terminee',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut',
            'date_fin_reelle' => 'nullable|date|after:date_debut',
            'statut' => 'required|in:actif,termine,suspendu',
        ]);

        $ancienneArtisteId = $projet->artiste_id;
        $ancienneEtape = $projet->etape;
        
        $projet->update($request->all());

        // Si l'étape a changé, enregistrer l'historique
        if ($ancienneEtape !== $request->etape) {
            $projet->updateEtape($request->etape, $request->commentaire ?? null);
        }

        // Notifier l'artiste si l'artiste a changé ou si l'étape a changé
        if ($projet->artiste && ($ancienneArtisteId != $projet->artiste_id || $ancienneEtape !== $request->etape)) {
            $projet->artiste->notify(new \App\Notifications\ProjetEtapeUpdatedNotification($projet));
        }

        Log::info('Projet mis à jour', [
            'projet_id' => $projet->id,
            'updated_by' => Auth::id(),
            'ancienne_etape' => $ancienneEtape,
            'nouvelle_etape' => $request->etape
        ]);

        return redirect()->route('projets.show', $projet)->with('success', 'Projet mis à jour avec succès.');
    }

    public function updateEtape(Request $request, Projet $projet)
    {
        Gate::authorize('update', $projet);
        $request->validate([
            'etape' => 'required|in:enregistrement,mixage,mastering,distribution_en_cours,distribution_terminee',
            'commentaire' => 'nullable|string',
        ]);
        // Sécurité : seul l'admin peut passer à distribution_en_cours ou distribution_terminee
        if (in_array($request->etape, ['distribution_en_cours', 'distribution_terminee']) && !Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Seul un administrateur peut passer à cette étape.');
        }
        $projet->updateEtape($request->etape, $request->commentaire);
        // Notification à l'artiste
        if ($projet->artiste) {
            $projet->artiste->notify(new \App\Notifications\ProjetEtapeUpdatedNotification($projet));
        }
        Log::info('Étape du projet mise à jour', [
            'projet_id' => $projet->id,
            'nouvelle_etape' => $request->etape,
            'updated_by' => Auth::id()
        ]);
        return redirect()->back()->with('success', 'Étape mise à jour avec succès.');
    }

    // Nouvelle méthode ajoutée
    public function updateEtapeSimple(Request $request, Projet $projet)
    {
        $request->validate([
            'etape' => 'required|string',
        ]);

        $projet->etape = $request->input('etape');
        $projet->save();

        return back()->with('success', 'Étape mise à jour avec succès.');
    }

    public function uploadMedia(Request $request, Projet $projet)
    {
        Gate::authorize('update', $projet);
        
        // Vérifier que le projet est au mastering
        if ($projet->etape !== 'mastering') {
            return redirect()->back()->with('error', 'Les fichiers ne peuvent être uploadés qu\'à l\'étape mastering.');
        }

        // Validation des fichiers
        $request->validate([
            'fichier_audio' => 'nullable|mimes:mp3,wav,aac,flac|max:512000', // 500MB max
            'fichier_video' => 'nullable|mimes:mp4,avi,mov,mkv|max:1024000', // 1GB max
        ]);

        // Vérifier qu'au moins un fichier est fourni
        if (!$request->hasFile('fichier_audio') && !$request->hasFile('fichier_video')) {
            return redirect()->back()->with('error', 'Veuillez sélectionner au moins un fichier à uploader.');
        }

        $updateData = [];

        // Upload du fichier audio
        if ($request->hasFile('fichier_audio')) {
            $audioFile = $request->file('fichier_audio');
            $audioPath = $audioFile->store('projets/audio', 'local');
            
            $updateData['fichier_audio_path'] = $audioPath;
            $updateData['fichier_audio_nom_original'] = $audioFile->getClientOriginalName();
            $updateData['fichier_audio_taille'] = $audioFile->getSize();
            $updateData['fichier_audio_uploaded_at'] = now();
        }

        // Upload du fichier vidéo
        if ($request->hasFile('fichier_video')) {
            $videoFile = $request->file('fichier_video');
            $videoPath = $videoFile->store('projets/video', 'local');
            
            $updateData['fichier_video_path'] = $videoPath;
            $updateData['fichier_video_nom_original'] = $videoFile->getClientOriginalName();
            $updateData['fichier_video_taille'] = $videoFile->getSize();
            $updateData['fichier_video_uploaded_at'] = now();
        }

        // Mettre à jour le projet
        $projet->update($updateData);

        Log::info('Fichiers médias uploadés', [
            'projet_id' => $projet->id,
            'uploaded_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Fichiers uploadés avec succès.');
    }

    public function downloadAudio(Projet $projet)
    {
        // Seuls les admins et responsables peuvent télécharger
        if (!Auth::user()->isAdmin() && !Auth::user()->isResponsableStudio()) {
            abort(403);
        }

        if (!$projet->hasFichierAudio()) {
            abort(404, 'Fichier audio non trouvé');
        }

        $filePath = storage_path('app/' . $projet->fichier_audio_path);
        
        return response()->download($filePath, $projet->fichier_audio_nom_original);
    }

    public function downloadVideo(Projet $projet)
    {
        // Seuls les admins et responsables peuvent télécharger
        if (!Auth::user()->isAdmin() && !Auth::user()->isResponsableStudio()) {
            abort(403);
        }

        if (!$projet->hasFichierVideo()) {
            abort(404, 'Fichier vidéo non trouvé');
        }

        $filePath = storage_path('app/' . $projet->fichier_video_path);
        
        return response()->download($filePath, $projet->fichier_video_nom_original);
    }

    public function validateFiles(Request $request, Projet $projet)
    {
        Gate::authorize('admin-access');

        $request->validate([
            'valide' => 'required|boolean',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $projet->update([
            'fichiers_valides_admin' => $request->valide,
            'commentaire_admin_fichiers' => $request->commentaire,
        ]);

        // Si validé, passer automatiquement à distribution_en_cours
        if ($request->valide) {
            $projet->updateEtape('distribution_en_cours', 'Fichiers validés par l\'administrateur');
        }

        Log::info('Validation des fichiers par l\'admin', [
            'projet_id' => $projet->id,
            'valide' => $request->valide,
            'admin_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Validation des fichiers enregistrée.');
    }
}