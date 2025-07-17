<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Projet;

class ProjetEtapeUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $projetId;
    public $titre;
    public $etape;
    public $etapeLabel;
    public $etapeColor;

    public function __construct(Projet $projet)
    {
        $this->projetId = $projet->id;
        $this->titre = $projet->titre;
        $this->etape = $projet->etape;
        $this->etapeLabel = $projet->etape_label;
        $this->etapeColor = $projet->etape_color;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Mise à jour de l\'étape de votre projet')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('L\'étape de votre projet "' . $this->titre . '" a été mise à jour.')
            ->line('Nouvelle étape : ' . $this->etapeLabel)
            ->line('Couleur associée : ' . $this->etapeColor);

        if ($this->etape === 'distribution_terminee') {
            $mail->line('🎉 Votre musique est maintenant disponible sur toutes les plateformes de streaming musical dans le monde !');
        }

        $mail->action('Voir le projet', url(route('projets.show', $this->projetId)))
            ->line('Merci de suivre l\'avancement de votre projet sur la plateforme.');

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'projet_id' => $this->projetId,
            'titre' => $this->titre,
            'etape' => $this->etape,
            'etape_label' => $this->etapeLabel,
            'etape_color' => $this->etapeColor,
        ];
    }
}
