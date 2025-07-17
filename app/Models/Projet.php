<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'description', 'etape', 'artiste_id', 'responsable_id',
        'date_debut', 'date_fin_prevue', 'date_fin_reelle', 'statut',
        'fichier_audio_path', 'fichier_video_path', 'fichier_audio_nom_original',
        'fichier_video_nom_original', 'fichier_audio_taille', 'fichier_video_taille',
        'fichier_audio_uploaded_at', 'fichier_video_uploaded_at', 'fichiers_valides_admin',
        'commentaire_admin_fichiers'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
        'fichier_audio_uploaded_at' => 'datetime',
        'fichier_video_uploaded_at' => 'datetime',
        'fichiers_valides_admin' => 'boolean',
    ];

    public function artiste()
    {
        return $this->belongsTo(User::class, 'artiste_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function etapes()
    {
        return $this->hasMany(ProjetEtape::class);
    }

    public function getEtapeColorAttribute()
    {
        return [
            'enregistrement' => 'blue',
            'mixage' => 'deeppink',
            'mastering' => 'gold',
            'distribution_en_cours' => 'orange',
            'distribution_terminee' => 'green'
        ][$this->etape] ?? 'black';
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

    public function updateEtape($nouvelleEtape, $commentaire = null, $userId = null)
    {
        $ancienneEtape = $this->etape;
        $this->update(['etape' => $nouvelleEtape]);

        // Enregistrer l'historique
        $this->etapes()->create([
            'etape' => $nouvelleEtape,
            'modifie_par' => $userId ?? auth()->id(),
            'commentaire' => $commentaire
        ]);

        // Notifier l'artiste si l'étape a changé
        if ($ancienneEtape !== $nouvelleEtape) {
            // TODO: Implémenter la notification
        }

        return $this;
    }

    // Méthodes pour les fichiers média
    public function hasFichierAudio()
    {
        return !empty($this->fichier_audio_path) && file_exists(storage_path('app/' . $this->fichier_audio_path));
    }

    public function hasFichierVideo()
    {
        return !empty($this->fichier_video_path) && file_exists(storage_path('app/' . $this->fichier_video_path));
    }

    public function getAudioSizeFormatted()
    {
        if (!$this->fichier_audio_taille) return null;
        return $this->formatFileSize($this->fichier_audio_taille);
    }

    public function getVideoSizeFormatted()
    {
        if (!$this->fichier_video_taille) return null;
        return $this->formatFileSize($this->fichier_video_taille);
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function canUploadFiles()
    {
        return $this->etape === 'mastering' && !$this->hasFichierAudio() && !$this->hasFichierVideo();
    }

    public function getAudioUrl()
    {
        return $this->hasFichierAudio() ? url('projets/' . $this->id . '/download-audio') : null;
    }

    public function getVideoUrl()
    {
        return $this->hasFichierVideo() ? url('projets/' . $this->id . '/download-video') : null;
    }
}