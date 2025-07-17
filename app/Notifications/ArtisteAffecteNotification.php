<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class ArtisteAffecteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $artiste;

    public function __construct(User $artiste)
    {
        $this->artiste = $artiste;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvel artiste affecté')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un nouvel artiste vous a été affecté :')
            ->line('Nom : ' . $this->artiste->name)
            ->line('Email : ' . $this->artiste->email)
            ->action('Voir l’artiste', url('/'))
            ->line('Merci de prendre contact avec cet artiste pour démarrer la collaboration.');
    }

    public function toArray($notifiable)
    {
        return [
            'artiste_id' => $this->artiste->id,
            'artiste_name' => $this->artiste->name,
            'artiste_email' => $this->artiste->email,
        ];
    }
}
