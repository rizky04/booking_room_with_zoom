<?php

namespace App\Services;

use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmation;
use App\Mail\BookingReminder;
use App\Mail\BookingVerified;
use App\Mail\AdminNotification;
use App\Models\Booking;
use App\Models\BookingNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendBookingConfirmation(Booking $booking): void
    {
        try {
            Mail::to($booking->email)->send(new BookingConfirmation($booking));
            $this->logNotification($booking, 'confirmation', $booking->email, 'sent');
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
            $this->logNotification($booking, 'confirmation', $booking->email, 'failed', $e->getMessage());
        }
    }

    public function sendBookingVerified(Booking $booking): void
    {
        try {
            Mail::to($booking->email)->send(new BookingVerified($booking));

            $adminEmail = config('booking.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new AdminNotification($booking));
                $this->logNotification($booking, 'admin_notification', $adminEmail, 'sent');
            }
        } catch (\Exception $e) {
            Log::error('Failed to send booking verified email', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    public function sendBookingCancelled(Booking $booking): void
    {
        try {
            Mail::to($booking->email)->send(new BookingCancelled($booking));
            $this->logNotification($booking, 'cancelled', $booking->email, 'sent');
        } catch (\Exception $e) {
            Log::error('Failed to send booking cancelled email', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
            $this->logNotification($booking, 'cancelled', $booking->email, 'failed', $e->getMessage());
        }
    }

    public function sendReminder(Booking $booking, string $type): void
    {
        try {
            Mail::to($booking->email)->send(new BookingReminder($booking, $type));
            $this->logNotification($booking, $type, $booking->email, 'sent');
        } catch (\Exception $e) {
            Log::error('Failed to send reminder', ['booking_id' => $booking->id, 'type' => $type, 'error' => $e->getMessage()]);
            $this->logNotification($booking, $type, $booking->email, 'failed', $e->getMessage());
        }
    }

    private function logNotification(Booking $booking, string $type, string $email, string $status, ?string $error = null): void
    {
        BookingNotification::create([
            'booking_id'      => $booking->id,
            'type'            => $type,
            'recipient_email' => $email,
            'status'          => $status,
            'sent_at'         => $status === 'sent' ? now() : null,
            'error_message'   => $error,
        ]);
    }
}
