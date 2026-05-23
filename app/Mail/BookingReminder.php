<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public \App\Models\Booking $booking,
        public string $reminderType = 'reminder_24h'
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->reminderType === 'reminder_1h' ? '1 Jam' : '24 Jam';
        return new Envelope(
            subject: "Pengingat Meeting {$label} Lagi - " . $this->booking->booking_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking-reminder',
            with: [
                'booking'      => $this->booking,
                'reminderType' => $this->reminderType,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
