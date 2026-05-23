<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;

class AvailabilityService
{
    public function isRoomAvailable(int $roomId, string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('room_id', $roomId)
            ->where('date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($inner) use ($startTime, $endTime) {
                    $inner->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }

    public function getAvailableRooms(string $date, string $startTime, string $endTime): \Illuminate\Database\Eloquent\Collection
    {
        $bookedRoomIds = Booking::where('date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            })
            ->pluck('room_id');

        return Room::active()->whereNotIn('id', $bookedRoomIds)->get();
    }

    public function getRoomSchedule(int $roomId, string $date): \Illuminate\Database\Eloquent\Collection
    {
        return Booking::where('room_id', $roomId)
            ->where('date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('start_time')
            ->get();
    }

    public function getMonthlySchedule(int $roomId, int $year, int $month): array
    {
        $bookings = Booking::where('room_id', $roomId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->groupBy(fn($b) => $b->date->format('Y-m-d'));

        return $bookings->toArray();
    }

    public function isWithinOperatingHours(string $startTime, string $endTime): bool
    {
        $opStart = config('booking.booking_rules.operating_hours.start', '07:00');
        $opEnd   = config('booking.booking_rules.operating_hours.end', '21:00');

        return $startTime >= $opStart && $endTime <= $opEnd;
    }

    public function isValidDuration(string $startTime, string $endTime): bool
    {
        $start = Carbon::parse($startTime);
        $end   = Carbon::parse($endTime);

        if ($end <= $start) {
            return false;
        }

        $minutes = $start->diffInMinutes($end);
        $minMin  = config('booking.booking_rules.min_duration_min', 30);
        $maxMin  = config('booking.booking_rules.max_duration_hours', 8) * 60;

        return $minutes >= $minMin && $minutes <= $maxMin;
    }

    public function isValidDate(string $date): bool
    {
        $bookingDate = Carbon::parse($date);
        $today       = Carbon::today();
        $maxDate     = Carbon::today()->addDays(config('booking.booking_rules.max_advance_days', 90));

        if ($bookingDate->lt($today)) {
            return false;
        }

        $allowedDays = config('booking.booking_rules.allowed_days', [1, 2, 3, 4, 5, 6]);
        if (!in_array($bookingDate->dayOfWeek, $allowedDays)) {
            return false;
        }

        return $bookingDate->lte($maxDate);
    }
}
