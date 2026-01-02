<?php

namespace App\Mail;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reminder $reminder)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('common.document_reminder').': '.$this->reminder->document->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.documents.reminder',
        );
    }
}
