<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\Invitee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public Invitee $invitee,
        public string $attachmentPath,
        public string $attachmentFilename,
        public string $attachmentMime = 'image/png',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your invitation: '.$this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation-card',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->attachmentPath)
                ->as($this->attachmentFilename)
                ->withMime($this->attachmentMime),
        ];
    }
}
