<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

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
        $year  = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

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
