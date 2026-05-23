@extends('layouts.admin')

@section('title', 'Detail Booking')

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.bookings') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Semua Booking
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main detail --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Header card --}}
        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-xs text-gray-400 font-mono mb-1">{{ $booking->booking_code }}</p>
                    <h2 class="text-xl font-bold text-gray-900">{{ $booking->title }}</h2>
                </div>
                <span class="badge-{{ $booking->status }} text-sm px-3 py-1">{{ ucfirst($booking->status) }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Pemesan</p>
                    <p class="font-medium text-gray-900">{{ $booking->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Email</p>
                    <p class="font-medium text-gray-900">{{ $booking->email }}</p>
                </div>
                @if($booking->phone)
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Telepon</p>
                    <p class="font-medium text-gray-900">{{ $booking->phone }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-400 text-xs mb-0.5">Peserta</p>
                    <p class="font-medium text-gray-900">{{ $booking->attendees }} orang</p>
                </div>
            </div>

            @if($booking->description)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-gray-400 text-xs mb-1">Keterangan</p>
                <p class="text-sm text-gray-700">{{ $booking->description }}</p>
            </div>
            @endif
        </div>

        {{-- Booking detail --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Detail Reservasi</h3>
            </div>
            <div class="divide-y divide-gray-100 text-sm">
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Ruang</span>
                    <span class="font-medium text-gray-900">{{ $booking->room->name }}</span>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Lokasi</span>
                    <span class="font-medium text-gray-900">{{ $booking->room->location ?? '—' }}</span>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Kapasitas Ruang</span>
                    <span class="font-medium text-gray-900">{{ $booking->room->capacity }} orang</span>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="font-medium text-gray-900">{{ $booking->date->format('l, d F Y') }}</span>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Waktu</span>
                    <span class="font-medium text-gray-900">
                        {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}
                        ({{ $booking->duration_minutes }} menit)
                    </span>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Zoom</span>
                    <span class="font-medium text-gray-900">{{ $booking->enable_zoom ? 'Ya' : 'Tidak' }}</span>
                </div>
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Dibuat</span>
                    <span class="font-medium text-gray-900">{{ $booking->created_at->format('d M Y H:i') }}</span>
                </div>
                @if($booking->verified_at)
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Dikonfirmasi</span>
                    <span class="font-medium text-gray-900">{{ $booking->verified_at->format('d M Y H:i') }}</span>
                </div>
                @endif
                @if($booking->cancel_reason)
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Alasan Batal</span>
                    <span class="font-medium text-gray-900">{{ $booking->cancel_reason }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Zoom Meeting --}}
        @if($booking->zoomMeeting)
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Zoom Meeting</h3>
            </div>
            <div class="divide-y divide-gray-100 text-sm">
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Meeting ID</span>
                    <span class="font-mono font-medium text-gray-900">{{ $booking->zoomMeeting->zoom_meeting_id }}</span>
                </div>
                @if($booking->zoomMeeting->password)
                <div class="flex justify-between px-5 py-3">
                    <span class="text-gray-500">Password</span>
                    <span class="font-medium text-gray-900">{{ $booking->zoomMeeting->password }}</span>
                </div>
                @endif
                <div class="px-5 py-3">
                    <span class="text-gray-500 block mb-1">Join URL</span>
                    <a href="{{ $booking->zoomMeeting->join_url }}" target="_blank"
                       class="text-blue-600 hover:text-blue-700 text-xs break-all">
                        {{ $booking->zoomMeeting->join_url }}
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Notification Log --}}
        @if($booking->notifications->isNotEmpty())
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Log Notifikasi</h3>
            </div>
            <div class="divide-y divide-gray-100 text-sm">
                @foreach($booking->notifications as $notif)
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <p class="font-medium text-gray-900">{{ str_replace('_', ' ', ucfirst($notif->type)) }}</p>
                        <p class="text-xs text-gray-400">{{ $notif->recipient_email }}</p>
                    </div>
                    <div class="text-right">
                        <span class="{{ $notif->status === 'sent' ? 'badge-confirmed' : 'badge-cancelled' }}">
                            {{ ucfirst($notif->status) }}
                        </span>
                        @if($notif->sent_at)
                        <p class="text-xs text-gray-400 mt-1">{{ $notif->sent_at->format('d M H:i') }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar: Actions --}}
    <div class="space-y-4">
        @if(in_array($booking->status, ['pending', 'confirmed']))
        <div class="card p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Aksi</h3>

            <div x-data="{ showCancel: false }">
                <button @click="showCancel = !showCancel"
                        class="w-full btn-danger text-sm py-2.5">
                    Batalkan Booking
                </button>

                <div x-show="showCancel" x-cloak class="mt-4">
                    <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-xs">Alasan Pembatalan *</label>
                            <textarea name="reason" rows="3" required
                                      placeholder="Tulis alasan pembatalan..."
                                      class="form-input text-sm"></textarea>
                            @error('reason') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-medium py-2 rounded-lg text-sm transition-colors">
                            Konfirmasi Pembatalan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="card p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Info Ruang</h3>
            <div class="space-y-2 text-sm">
                <div>
                    <p class="text-gray-400 text-xs">Nama</p>
                    <p class="font-medium text-gray-900">{{ $booking->room->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Kapasitas</p>
                    <p class="font-medium text-gray-900">{{ $booking->room->capacity }} orang</p>
                </div>
                @if($booking->room->facilities)
                <div>
                    <p class="text-gray-400 text-xs mb-1">Fasilitas</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($booking->room->facilities as $f)
                            <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs">{{ $f }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
