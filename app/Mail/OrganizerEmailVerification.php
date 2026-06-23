<?php

namespace App\Mail;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrganizerEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public string $verifyUrl;

    public function __construct(public Admin $admin)
    {
        $this->verifyUrl = url(route('admin.verify-email', [
            'id'    => $admin->id,
            'token' => $admin->email_verification_token,
        ], false));
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verify Your Email — ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.organizer.verify');
    }

    public function attachments(): array
    {
        return [];
    }
}
