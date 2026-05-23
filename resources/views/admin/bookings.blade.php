@extends('layouts.admin')

@section('title', 'Semua Booking')

@section('content')

{{-- Filters --}}
<div class="card p-4 mb-5">
    <form method="GET" action="{{ route('admin.bookings') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label text-xs">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nama, email, kode..."
                   class="form-input text-sm py-2 w-52">
        </div>
        <div>
            <label class="form-label text-xs">Status</label>
            <select name="status" class="form-input text-sm py-2">
                <option value="">Semua Status</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmed</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Ruang</label>
            <select name="room_id" class="form-input text-sm py-2">
                <option value="">Semua Ruang</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" @selected(request('room_id') == $room->id)>{{ $room->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="form-input text-sm py-2">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary text-sm py-2">Filter</button>
            <a href="{{ route('admin.bookings') }}" class="btn-secondary text-sm py-2">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">
            Daftar Booking
            <span class="text-gray-400 font-normal text-sm">({{ $bookings->total() }} total)</span>
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Kode</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Pemesan</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Ruang</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal & Waktu</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Topik</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Zoom</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $booking->booking_code }}</td>
                    <td class="px-5 py-3">
                        <div class="font-medium text-gray-900">{{ $booking->name }}</div>
                        <div class="text-xs text-gray-400">{{ $booking->email }}</div>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $booking->room->name }}</td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="text-gray-900">{{ $booking->date->format('d M Y') }}</div>
                        <div class="text-xs text-gray-400">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}</div>
                    </td>
                    <td class="px-5 py-3 text-gray-600 max-w-[160px] truncate">{{ $booking->title }}</td>
                    <td class="px-5 py-3 text-center">
                        @if($booking->enable_zoom)
                            <svg class="w-4 h-4 text-blue-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
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
                    <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                        Tidak ada booking ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
