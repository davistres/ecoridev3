<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class SatisfactionSurveyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $passengerName;
    public $driverName;
    public $cityDep;
    public $cityArr;
    public $departureDate;
    public $loginUrl;

    public function __construct($passengerName, $driverName, $cityDep, $cityArr, $departureDate)
    {
        $this->passengerName = $passengerName;
        $this->driverName = $driverName;
        $this->cityDep = $cityDep;
        $this->cityArr = $cityArr;
        $this->departureDate = $departureDate;
        $this->loginUrl = route('login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'noreply@ecoride.fr'), env('MAIL_FROM_NAME', 'EcoRide')),
            subject: 'Formulaire de satisfaction obligatoire - EcoRide',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.satisfaction-survey',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

