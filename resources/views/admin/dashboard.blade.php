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

{{-- Resource Calendar --}}
<div class="card overflow-hidden mb-6"
     x-data="{
        date: '{{ now()->toDateString() }}',
        rooms: [],
        bookings: [],
        loading: false,
        hours: Array.from({length: 14}, (_, i) => i + 7), /* 07–20 */

        get dateLabel() {
            return new Date(this.date + 'T00:00:00').toLocaleDateString('id-ID', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            });
        },
        get isToday() {
            return this.date === new Date().toISOString().slice(0,10);
        },

        prevDay() {
            const d = new Date(this.date + 'T00:00:00');
            d.setDate(d.getDate() - 1);
            this.date = d.toISOString().slice(0,10);
            this.load();
        },
        nextDay() {
            const d = new Date(this.date + 'T00:00:00');
            d.setDate(d.getDate() + 1);
            this.date = d.toISOString().slice(0,10);
            this.load();
        },
        goToday() {
            this.date = new Date().toISOString().slice(0,10);
            this.load();
        },

        async load() {
            this.loading = true;
            try {
                const res = await fetch('/admin/calendar-data?date=' + this.date);
                const data = await res.json();
                this.rooms    = data.rooms    || [];
                this.bookings = data.bookings || [];
            } catch(e) {}
            this.loading = false;
        },

        bookingsForRoom(roomId) {
            return this.bookings.filter(b => b.room_id === roomId);
        },

        /* Convert HH:MM to minutes from 07:00 */
        toMin(t) {
            const [h, m] = t.split(':').map(Number);
            return (h * 60 + m) - 7 * 60;
        },

        /* top% and height% within 07:00-21:00 (840 min) */
        slotStyle(b) {
            const top    = this.toMin(b.start_time) / 840 * 100;
            const height = (this.toMin(b.end_time) - this.toMin(b.start_time)) / 840 * 100;
            return 'top:' + top + '%;height:' + height + '%;';
        },

        slotColor(status) {
            return status === 'confirmed'
                ? 'bg-blue-100 border-blue-300 text-blue-800'
                : 'bg-yellow-100 border-yellow-300 text-yellow-800';
        },

        init() { this.load(); }
     }">

    {{-- Calendar header --}}
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <h2 class="font-semibold text-gray-900">Kalender Ruang</h2>
            <span x-show="loading" class="text-xs text-gray-400 flex items-center gap-1">
                <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Memuat...
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="prevDay()"
                    class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-100 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <div class="text-sm font-semibold text-gray-900 min-w-52 text-center" x-text="dateLabel"></div>
            <button type="button" @click="nextDay()"
                    class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-100 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <button type="button" @click="goToday()"
                    :class="isToday ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 h-8 rounded-lg text-xs font-semibold transition-colors">
                Hari Ini
            </button>
        </div>
    </div>

    {{-- Calendar grid --}}
    <div class="overflow-x-auto">
        <div x-show="rooms.length === 0 && !loading" class="px-5 py-10 text-center text-gray-400 text-sm">
            Tidak ada ruang aktif
        </div>

        <template x-if="rooms.length > 0">
            <div class="flex" style="min-width: 600px;">

                {{-- Time axis --}}
                <div class="shrink-0 w-14 border-r border-gray-100">
                    {{-- Header spacer --}}
                    <div class="h-10 border-b border-gray-100"></div>
                    {{-- Hour labels --}}
                    <div class="relative" style="height: 560px;">
                        <template x-for="h in hours" :key="h">
                            <div class="absolute w-full flex items-start justify-end pr-2"
                                 :style="'top: ' + ((h - 7) / 14 * 100) + '%;'">
                                <span class="text-xs text-gray-400 font-medium -mt-2"
                                      x-text="String(h).padStart(2,'0') + ':00'"></span>
                            </div>
                        </template>
                        {{-- 21:00 label --}}
                        <div class="absolute w-full flex items-start justify-end pr-2" style="top: 100%;">
                            <span class="text-xs text-gray-400 font-medium -mt-2">21:00</span>
                        </div>
                    </div>
                </div>

                {{-- Room columns --}}
                <div class="flex flex-1">
                    <template x-for="room in rooms" :key="room.id">
                        <div class="flex-1 min-w-32 border-r border-gray-100 last:border-r-0">
                            {{-- Room header --}}
                            <div class="h-10 border-b border-gray-100 px-2 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-xs font-semibold text-gray-700 truncate" x-text="room.name"></div>
                                    <div class="text-xs text-gray-400 truncate" x-show="room.location" x-text="room.location"></div>
                                </div>
                            </div>

                            {{-- Time grid + bookings --}}
                            <div class="relative" style="height: 560px;">
                                {{-- Hour grid lines --}}
                                <template x-for="h in hours" :key="h">
                                    <div class="absolute w-full border-t border-gray-100"
                                         :style="'top: ' + ((h - 7) / 14 * 100) + '%;'"></div>
                                </template>
                                {{-- Half-hour dashed lines --}}
                                <template x-for="h in hours" :key="'h_' + h">
                                    <div class="absolute w-full border-t border-gray-50 border-dashed"
                                         :style="'top: ' + ((h - 7 + 0.5) / 14 * 100) + '%;'"></div>
                                </template>
                                {{-- Bottom border --}}
                                <div class="absolute w-full border-t border-gray-100" style="top:100%;"></div>

                                {{-- Current time indicator --}}
                                <template x-if="isToday">
                                    <div class="absolute w-full z-10 pointer-events-none"
                                         :style="'top: ' + Math.min(Math.max(((new Date().getHours() * 60 + new Date().getMinutes()) - 420) / 840 * 100, 0), 100) + '%;'">
                                        <div class="h-0.5 bg-red-500 w-full relative">
                                            <div class="absolute -left-1 -top-1.5 w-3 h-3 rounded-full bg-red-500"></div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Bookings --}}
                                <template x-for="b in bookingsForRoom(room.id)" :key="b.id">
                                    <a :href="'/admin/bookings/' + b.id"
                                       :class="slotColor(b.status)"
                                       :style="'position:absolute;left:4px;right:4px;border-radius:6px;border:1px solid;padding:4px 6px;overflow:hidden;z-index:5;' + slotStyle(b)"
                                       class="block text-xs hover:brightness-95 transition-all">
                                        <div class="font-semibold leading-tight truncate" x-text="b.start_time + '–' + b.end_time"></div>
                                        <div class="truncate leading-tight" x-text="b.title"></div>
                                        <div class="truncate leading-tight opacity-70" x-text="b.name"></div>
                                    </a>
                                </template>

                            </div>
                        </div>
                    </template>
                </div>

            </div>
        </template>
    </div>

    {{-- Legend --}}
    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center gap-5 text-xs text-gray-500">
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded bg-blue-100 border border-blue-300 inline-block"></span> Confirmed
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-300 inline-block"></span> Pending
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Waktu sekarang
        </span>
        <span class="ml-auto text-gray-400">Klik booking untuk melihat detail</span>
    </div>
</div>

{{-- Bottom section: recent bookings + today's list --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

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
                        <td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada booking</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-5">
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
                <div class="px-5 py-6 text-center text-gray-400 text-sm">Tidak ada jadwal hari ini</div>
                @endforelse
            </div>
        </div>

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
