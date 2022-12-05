<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageGoogle extends Mailable
{
    use Queueable, SerializesModels;

    public $titre; // Données pour la vue
    public $data; // Données pour la vue
    public $expediteur; // Expéditeur
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($titre, $data, $expediteur)
    {
        $this->data = $data;
        $this->titre = $titre;
        $this->expediteur = $expediteur;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->expediteur) // L'expéditeur
                    ->subject("Notification") // Le sujet
                    ->view("emails.message"); // La vue
    }
}
