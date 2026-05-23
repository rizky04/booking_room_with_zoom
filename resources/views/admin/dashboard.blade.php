@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Booking Hari Ini</div>
        <div class="text-3xl font-bold text-gray-900">{{ $todayBookings }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Menunggu Konfirmasi</div>
        <div class="text-3xl font-bold text-yellow-500">{{ $pendingCount }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Ruang Tersedia</div>
        <div class="text-3xl font-bold text-green-600">{{ $availableRooms }}/{{ $rooms->count() }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Bulan Ini</div>
        <div class="text-3xl font-bold text-blue-600">{{ $monthBookings }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Recent Bookings --}}
    <div class="lg:col-span-2 card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Booking Terbaru</h2>
            <a href="{{ route('admin.bookings') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Pemesan</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Ruang</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentBookings as $booking)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-900">{{ $booking->name }}</div>
                            <div class="text-xs text-gray-400">{{ $booking->title }}</div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $booking->room->name }}</td>
                        <td class="px-5 py-3 text-gray-600 whitespace-nowrap">
                            {{ $booking->date->format('d M') }}, {{ substr($booking->start_time, 0, 5) }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.bookings.show', $booking->id) }}"
                               class="text-blue-600 hover:text-blue-700 font-medium text-xs">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm">
                            Belum ada booking
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sidebar: Jadwal Hari Ini + Room Status --}}
    <div class="space-y-5">

        {{-- Jadwal Hari Ini --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Jadwal Hari Ini</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                @forelse($todaySchedule as $booking)
                <div class="px-5 py-3">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-xs font-semibold text-gray-900 tabular-nums">
                            {{ substr($booking->start_time, 0, 5) }}–{{ substr($booking->end_time, 0, 5) }}
                        </span>
                        <span class="badge-{{ $booking->status }} text-xs">{{ ucfirst($booking->status) }}</span>
                    </div>
                    <p class="text-sm text-gray-700 font-medium truncate">{{ $booking->title }}</p>
                    <p class="text-xs text-gray-400">{{ $booking->room->name }} · {{ $booking->name }}</p>
                </div>
                @empty
                <div class="px-5 py-6 text-center text-gray-400 text-sm">
                    Tidak ada jadwal hari ini
                </div>
                @endforelse
            </div>
        </div>

        {{-- Room Status --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Status Ruang</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($rooms as $room)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $room->name }}</p>
                        <p class="text-xs text-gray-400">Kap. {{ $room->capacity }} orang</p>
                    </div>
                    <span class="{{ $room->status === 'active' ? 'badge-confirmed' : 'badge-cancelled' }}">
                        {{ $room->status === 'active' ? 'Aktif' : ucfirst($room->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
