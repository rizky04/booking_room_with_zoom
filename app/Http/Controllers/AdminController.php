<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private AvailabilityService $availabilityService,
    ) {}

    public function dashboard()
    {
        $today = now()->toDateString();

        $todayBookings  = Booking::forDate($today)->whereIn('status', ['pending', 'confirmed'])->count();
        $pendingCount   = Booking::pending()->count();
        $monthBookings  = Booking::whereMonth('date', now()->month)->whereYear('date', now()->year)->count();
        $rooms          = Room::all();
        $availableRooms = Room::active()->count();

        $recentBookings = Booking::with('room')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $todaySchedule = Booking::with('room')
            ->forDate($today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('start_time')
            ->get();

        return view('admin.dashboard', compact(
            'todayBookings', 'pendingCount', 'monthBookings',
            'rooms', 'availableRooms', 'recentBookings', 'todaySchedule'
        ));
    }

    public function calendarData(Request $request)
    {
        $date  = $request->input('date', now()->toDateString());
        $rooms = \App\Models\Room::active()->orderBy('name')->get();

        $bookings = \App\Models\Booking::with('room')
            ->where('date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'date'     => $date,
            'rooms'    => $rooms->map(fn($r) => ['id' => $r->id, 'name' => $r->name, 'location' => $r->location]),
            'bookings' => $bookings->map(fn($b) => [
                'id'         => $b->id,
                'room_id'    => $b->room_id,
                'title'      => $b->title,
                'name'       => $b->name,
                'start_time' => substr($b->start_time, 0, 5),
                'end_time'   => substr($b->end_time, 0, 5),
                'status'     => $b->status,
            ]),
        ]);
    }

    public function bookings(Request $request)
    {
        $query = Booking::with('room')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('booking_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(20)->withQueryString();
        $rooms    = Room::orderBy('name')->get();

        return view('admin.bookings', compact('bookings', 'rooms'));
    }

    public function showBooking(int $id)
    {
        $booking = Booking::with(['room', 'zoomMeeting', 'notifications'])->findOrFail($id);
        return view('admin.bookings-detail', compact('booking'));
    }

    public function createBooking()
    {
        $rooms = Room::active()->orderBy('name')->get();
        return view('admin.bookings-create', compact('rooms'));
    }

    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'room_id'     => ['required', 'exists:rooms,id'],
            'name'        => ['required', 'string', 'max:100'],
            'email'       => ['required', 'email', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'date'        => ['required', 'date', 'after_or_equal:today'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
            'attendees'   => ['required', 'integer', 'min:1', 'max:500'],
            'enable_zoom' => ['nullable', 'boolean'],
        ], [
            'room_id.required'    => 'Ruang harus dipilih.',
            'room_id.exists'      => 'Ruang tidak valid.',
            'name.required'       => 'Nama pemesan wajib diisi.',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'title.required'      => 'Topik meeting wajib diisi.',
            'date.required'       => 'Tanggal wajib diisi.',
            'date.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
            'start_time.required' => 'Waktu mulai wajib diisi.',
            'end_time.required'   => 'Waktu selesai wajib diisi.',
            'end_time.after'      => 'Waktu selesai harus setelah waktu mulai.',
            'attendees.required'  => 'Jumlah peserta wajib diisi.',
        ]);

        $startTime = $data['start_time'] . ':00';
        $endTime   = $data['end_time'] . ':00';

        if (!$this->availabilityService->isRoomAvailable($data['room_id'], $data['date'], $startTime, $endTime)) {
            return back()->withInput()->withErrors(['date' => 'Ruang sudah dibooking pada waktu tersebut.']);
        }

        $data['start_time'] = $startTime;
        $data['end_time']   = $endTime;

        try {
            $booking = $this->bookingService->createBookingByAdmin($data);
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['date' => 'Ruang sudah dibooking pada waktu tersebut.']);
        }

        return redirect()->route('admin.bookings.show', $booking->id)
            ->with('success', 'Booking berhasil dibuat dan email konfirmasi telah dikirim.');
    }

    public function cancelBooking(Request $request, int $id)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $booking = Booking::whereIn('status', ['pending', 'confirmed'])->findOrFail($id);
        $this->bookingService->cancelByAdmin($booking, $request->reason, auth('admin')->id());

        return redirect()->route('admin.bookings.show', $id)
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    public function reports(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $monthlyStats = Booking::selectRaw('
                DATE(date) as booking_date,
                COUNT(*) as total,
                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending
            ')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->groupBy('booking_date')
            ->orderBy('booking_date')
            ->get();

        $roomStats = Booking::with('room')
            ->selectRaw('room_id, COUNT(*) as total_bookings')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('room_id')
            ->get();

        $totalThisMonth  = Booking::whereYear('date', $year)->whereMonth('date', $month)->count();
        $confirmedMonth  = Booking::confirmed()->whereYear('date', $year)->whereMonth('date', $month)->count();
        $cancelledMonth  = Booking::where('status', 'cancelled')->whereYear('date', $year)->whereMonth('date', $month)->count();

        return view('admin.reports', compact(
            'monthlyStats', 'roomStats', 'year', 'month',
            'totalThisMonth', 'confirmedMonth', 'cancelledMonth'
        ));
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
