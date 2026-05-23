<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private AvailabilityService $availabilityService,
    ) {}

    public function showForm()
    {
        $rooms = Room::active()->orderBy('name')->get();
        return view('booking.form', compact('rooms'));
    }

    public function store(StoreBookingRequest $request)
    {
        $data = $request->validated();

        $room = Room::find($data['room_id']);
        if (!$room || !$room->isActive()) {
            return back()->withInput()->withErrors(['room_id' => 'Ruang meeting tidak tersedia.']);
        }

        if (!$this->availabilityService->isRoomAvailable($data['room_id'], $data['date'], $data['start_time'], $data['end_time'])) {
            return back()->withInput()->withErrors(['date' => 'Ruang meeting sudah dibooking pada waktu tersebut. Silakan pilih waktu lain.']);
        }

        if (!$this->availabilityService->isValidDuration($data['start_time'], $data['end_time'])) {
            return back()->withInput()->withErrors(['end_time' => 'Durasi meeting minimum 30 menit dan maksimum 8 jam.']);
        }

        $booking = $this->bookingService->createBooking($data);

        return redirect()->route('booking.pending', $booking->booking_code)
            ->with('success', 'Booking berhasil dibuat! Silakan cek email untuk konfirmasi.');
    }

    public function pending(string $code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();
        return view('booking.pending', compact('booking'));
    }

    public function verify(string $token)
    {
        $booking = $this->bookingService->verifyBooking($token);

        if (!$booking) {
            return view('booking.verify-failed');
        }

        return redirect()->route('booking.success', $booking->booking_code);
    }

    public function success(string $code)
    {
        $booking = Booking::with(['room', 'zoomMeeting'])
            ->where('booking_code', $code)
            ->where('status', 'confirmed')
            ->firstOrFail();

        return view('booking.success', compact('booking'));
    }

    public function cancelForm(string $token)
    {
        $booking = Booking::where('cancel_token', $token)
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        return view('booking.cancel', compact('booking'));
    }

    public function cancel(Request $request, string $token)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $booking = $this->bookingService->cancelBooking($token, $request->reason ?? '');

        if (!$booking) {
            return redirect()->route('booking.form')->with('error', 'Link pembatalan tidak valid atau sudah kadaluarsa.');
        }

        return view('booking.cancelled', compact('booking'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id'    => 'required|exists:rooms,id',
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
        ]);

        $available = $this->availabilityService->isRoomAvailable(
            $request->room_id,
            $request->date,
            $request->start_time,
            $request->end_time
        );

        $schedule = $this->availabilityService->getRoomSchedule($request->room_id, $request->date);

        return response()->json([
            'available' => $available,
            'schedule'  => $schedule->map(fn($b) => [
                'start_time' => $b->start_time,
                'end_time'   => $b->end_time,
                'title'      => $b->title,
            ]),
        ]);
    }
}
