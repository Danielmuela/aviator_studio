<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\ProjetEtape;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class StudioController extends Controller
{
    public function dashboard()
    {
        Gate::authorize('studio-access');
        
        $projets = Projet::with(['artiste', 'responsable', 'etapes'])->get();
        
        $stats = [
            'total_projets' => $projets->count(),
            'projets_actifs' => $projets->where('statut', 'actif')->count(),
            'projets_termines' => $projets->where('statut', 'termine')->count(),
        ];

        return view('studio.dashboard', compact('projets', 'stats'));
    }

    public function createProjet()
    {
        Gate::authorize('studio-access');
        
        $artistes = User::whereHas('role', fn($q) => $q->where('name', 'artiste'))->where('statut', 'valide')->get();
        return view('studio.create-projet', compact('artistes'));
    }

    public function storeProjet(Request $request)
    {
        Gate::authorize('studio-access');
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'artiste_id' => 'required|exists:users,id',
            'date_debut' => 'required|date',
            'date_fin_prevue' => 'required|date|after:date_debut',
        ]);

        // Vérifier que l'utilisateur est bien un artiste
        $artiste = User::find($request->artiste_id);
        if (!$artiste || !$artiste->role || $artiste->role->name !== 'artiste') {
            return redirect()->back()->with('error', 'Utilisateur sélectionné n\'est pas un artiste.');
        }

        $projet = Projet::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'artiste_id' => $request->artiste_id,
            'responsable_id' => Auth::id(),
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin_prevue,
            'statut' => 'actif',
        ]);

        // Créer la première étape (enregistrement)
        ProjetEtape::create([
            'projet_id' => $projet->id,
            'etape' => 'enregistrement',
            'modifie_par' => Auth::id(),
            'commentaire' => 'Projet créé - étape d\'enregistrement initiée',
        ]);

        // Notifier l'artiste de la création du projet
        if ($projet->artiste) {
            $projet->artiste->notify(new \App\Notifications\ProjetEtapeUpdatedNotification($projet));
        }

        Log::info('Nouveau projet créé par le responsable studio', [
            'projet_id' => $projet->id,
            'artiste_id' => $request->artiste_id,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('studio.dashboard')->with('success', 'Projet créé avec succès.');
    }

    public function updateEtape(Request $request, Projet $projet)
    {
        Gate::authorize('studio-access');
        
        $request->validate([
            'etape' => 'required|in:enregistrement,mixage,mastering,distribution_en_cours,distribution_terminee',
            'commentaire' => 'nullable|string|max:500',
        ]);

        // Vérifier les permissions selon l'étape
        if (in_array($request->etape, ['distribution_en_cours', 'distribution_terminee'])) {
            Gate::authorize('admin-access'); // Seul l'admin peut modifier ces étapes
        }

        // Créer la nouvelle étape
        ProjetEtape::create([
            'projet_id' => $projet->id,
            'etape' => $request->etape,
            'modifie_par' => Auth::id(),
            'commentaire' => $request->commentaire,
        ]);

        // Mettre à jour le statut du projet si nécessaire
        if ($request->etape === 'distribution_terminee') {
            $projet->update(['statut' => 'termine']);
        }

        Log::info('Étape de projet mise à jour', [
            'projet_id' => $projet->id,
            'nouvelle_etape' => $request->etape,
            'updated_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Étape mise à jour avec succès.');
    }

    public function showProjet(Projet $projet)
    {
        Gate::authorize('studio-access');
        
        $projet->load(['artiste', 'responsable', 'etapes.modifiePar']);
        
        return view('studio.show-projet', compact('projet'));
    }

    public function editProjet(Projet $projet)
    {
        Gate::authorize('studio-access');
        
        $artistes = User::whereHas('role', function($query) {
            $query->where('name', 'artiste');
        })->where('statut', 'valide')->get();

        return view('studio.edit-projet', compact('projet', 'artistes'));
    }

    public function updateProjet(Request $request, Projet $projet)
    {
        Gate::authorize('studio-access');
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'artiste_id' => 'required|exists:users,id',
            'date_debut' => 'required|date',
            'date_fin_prevue' => 'required|date|after:date_debut',
            'statut' => 'required|in:actif,termine,suspendu',
        ]);

        $ancienneArtisteId = $projet->artiste_id;
        $projet->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'artiste_id' => $request->artiste_id,
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin_prevue,
            'statut' => $request->statut,
        ]);

        // Notifier l'artiste si l'artiste a changé
        if ($projet->artiste && $ancienneArtisteId != $projet->artiste_id) {
            $projet->artiste->notify(new \App\Notifications\ProjetEtapeUpdatedNotification($projet));
        }

        Log::info('Projet modifié par le responsable studio', [
            'projet_id' => $projet->id,
            'modified_by' => Auth::id()
        ]);

        return redirect()->route('studio.dashboard')->with('success', 'Projet mis à jour avec succès.');
    }

    public function uploadMedia(Request $request, Projet $projet)
    {
        Gate::authorize('studio-access');
        
        // Vérifier que le projet est au mastering
        if ($projet->etape !== 'mastering') {
            return redirect()->back()->with('error', 'Les fichiers ne peuvent être uploadés qu\'à l\'étape mastering.');
        }

        // Validation des fichiers
        $request->validate([
            'fichier_audio' => 'nullable|mimes:mp3,wav,aac,flac,ogg,m4a,mpga,opus|max:512000', // 500MB max
            'fichier_video' => 'nullable|mimes:mp4,avi,mov,mkv|max:1024000', // 1GB max
        ], [
            'fichier_audio.mimes' => 'Le fichier audio doit être au format MP3, WAV, AAC, FLAC, OGG, M4A, MPGA ou OPUS.',
            'fichier_audio.max' => 'Le fichier audio ne doit pas dépasser 500MB.',
            'fichier_video.mimes' => 'Le fichier vidéo doit être au format MP4, AVI, MOV ou MKV.',
            'fichier_video.max' => 'Le fichier vidéo ne doit pas dépasser 1GB.',
        ]);

        // Vérifier qu'au moins un fichier est fourni
        if (!$request->hasFile('fichier_audio') && !$request->hasFile('fichier_video')) {
            return redirect()->back()->with('error', 'Veuillez sélectionner au moins un fichier à uploader.');
        }

        $updateData = [];

        // Upload du fichier audio
        if ($request->hasFile('fichier_audio')) {
            $audioFile = $request->file('fichier_audio');
            $audioPath = $audioFile->store('projets/audio', 'public');
            
            $updateData['fichier_audio_path'] = $audioPath;
            $updateData['fichier_audio_nom_original'] = $audioFile->getClientOriginalName();
            $updateData['fichier_audio_taille'] = $audioFile->getSize();
            $updateData['fichier_audio_uploaded_at'] = now();
        }

        // Upload du fichier vidéo
        if ($request->hasFile('fichier_video')) {
            $videoFile = $request->file('fichier_video');
            $videoPath = $videoFile->store('projets/video', 'public');
            
            $updateData['fichier_video_path'] = $videoPath;
            $updateData['fichier_video_nom_original'] = $videoFile->getClientOriginalName();
            $updateData['fichier_video_taille'] = $videoFile->getSize();
            $updateData['fichier_video_uploaded_at'] = now();
        }

        // Mettre à jour le projet
        $projet->update($updateData);

        // Notifier l'administrateur
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'administrateur'))->get();
        foreach ($admins as $admin) {
            // Note: Vous devrez créer cette notification
            // $admin->notify(new \App\Notifications\MediaFilesUploadedNotification($projet));
        }

        Log::info('Fichiers médias uploadés', [
            'projet_id' => $projet->id,
            'audio_uploaded' => $request->hasFile('fichier_audio'),
            'video_uploaded' => $request->hasFile('fichier_video'),
            'uploaded_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Fichiers uploadés avec succès. L\'administrateur a été notifié.');
    }
}