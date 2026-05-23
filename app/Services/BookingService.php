<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        private AvailabilityService $availabilityService,
        private ZoomService $zoomService,
        private EmailService $emailService,
    ) {}

    public function createBooking(array $data): Booking
    {
        $booking = Booking::create([
            'booking_code'       => $this->generateBookingCode(),
            'room_id'            => $data['room_id'],
            'name'               => $data['name'],
            'email'              => $data['email'],
            'phone'              => $data['phone'] ?? null,
            'title'              => $data['title'],
            'description'        => $data['description'] ?? null,
            'date'               => $data['date'],
            'start_time'         => $data['start_time'],
            'end_time'           => $data['end_time'],
            'attendees'          => $data['attendees'] ?? 1,
            'enable_zoom'        => !empty($data['enable_zoom']),
            'status'             => 'pending',
            'verification_token' => Str::random(64),
            'cancel_token'       => Str::random(64),
            'reschedule_token'   => Str::random(64),
        ]);

        $this->emailService->sendBookingConfirmation($booking);

        return $booking;
    }

    public function verifyBooking(string $token): Booking|false
    {
        $booking = Booking::where('verification_token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$booking) {
            return false;
        }

        $tokenAge = now()->diffInHours($booking->created_at);
        if ($tokenAge > config('booking.verification_token_expiry', 24)) {
            return false;
        }

        if (!$this->availabilityService->isRoomAvailable(
            $booking->room_id,
            $booking->date,
            $booking->start_time,
            $booking->end_time,
            $booking->id
        )) {
            return false;
        }

        $booking->update([
            'status'             => 'confirmed',
            'verified_at'        => now(),
            'email_verified_at'  => now(),
            'verification_token' => null,
        ]);

        if ($booking->enable_zoom) {
            $this->zoomService->createMeeting($booking);
        }

        // Refresh agar relasi zoomMeeting termuat sebelum email dikirim
        $booking->load(['room', 'zoomMeeting']);

        $this->emailService->sendBookingVerified($booking);

        return $booking;
    }

    public function cancelBooking(string $token, string $reason = ''): Booking|false
    {
        $booking = Booking::where('cancel_token', $token)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if (!$booking) {
            return false;
        }

        $booking->update([
            'status'        => 'cancelled',
            'cancelled_by'  => 'user',
            'cancel_reason' => $reason,
            'cancel_token'  => null,
        ]);

        if ($booking->zoomMeeting) {
            $this->zoomService->deleteMeeting($booking->zoomMeeting->zoom_meeting_id);
        }

        $this->emailService->sendBookingCancelled($booking);

        return $booking;
    }

    public function cancelByAdmin(Booking $booking, string $reason, int $adminId): void
    {
        $booking->update([
            'status'        => 'cancelled',
            'cancelled_by'  => 'admin',
            'cancel_reason' => $reason,
            'cancel_token'  => null,
        ]);

        if ($booking->zoomMeeting) {
            $this->zoomService->deleteMeeting($booking->zoomMeeting->zoom_meeting_id);
        }

        $this->emailService->sendBookingCancelled($booking);
    }

    private function generateBookingCode(): string
    {
        do {
            $code = 'BK-' . strtoupper(Str::random(8));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }
}
