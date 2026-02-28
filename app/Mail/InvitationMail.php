<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invitation $invitation;
    public string $inviteLink;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation->loadMissing(['inviter', 'colocation']);
        $this->inviteLink = route('invitations.show', $invitation->token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation EasyColoc'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation'
        );
    }
}
