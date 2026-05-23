@extends('layouts.app')

@section('title', 'Booking Dikonfirmasi')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-9 h-9 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Booking Dikonfirmasi!</h1>
            <p class="text-gray-500 mt-1">Meeting room Anda telah berhasil dipesan.</p>
        </div>

        {{-- Detail Booking --}}
        <div class="border border-gray-200 rounded-xl overflow-hidden mb-6">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-700">Detail Booking</span>
                    <span class="badge-confirmed">{{ $booking->booking_code }}</span>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="flex justify-between items-center px-5 py-3">
                    <span class="text-sm text-gray-500">Pemesan</span>
                    <span class="text-sm font-medium text-gray-900">{{ $booking->name }}</span>
                </div>
                <div class="flex justify-between items-center px-5 py-3">
                    <span class="text-sm text-gray-500">Ruang</span>
                    <span class="text-sm font-medium text-gray-900">{{ $booking->room->name }}</span>
                </div>
                @if($booking->room->location)
                <div class="flex justify-between items-center px-5 py-3">
                    <span class="text-sm text-gray-500">Lokasi</span>
                    <span class="text-sm font-medium text-gray-900">{{ $booking->room->location }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center px-5 py-3">
                    <span class="text-sm text-gray-500">Tanggal</span>
                    <span class="text-sm font-medium text-gray-900">{{ $booking->date->format('l, d F Y') }}</span>
                </div>
                <div class="flex justify-between items-center px-5 py-3">
                    <span class="text-sm text-gray-500">Waktu</span>
                    <span class="text-sm font-medium text-gray-900">
                        {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}
                        <span class="text-gray-400">({{ $booking->duration_minutes }} menit)</span>
                    </span>
                </div>
                <div class="flex justify-between items-center px-5 py-3">
                    <span class="text-sm text-gray-500">Topik</span>
                    <span class="text-sm font-medium text-gray-900">{{ $booking->title }}</span>
                </div>
                @if($booking->attendees > 1)
                <div class="flex justify-between items-center px-5 py-3">
                    <span class="text-sm text-gray-500">Peserta</span>
                    <span class="text-sm font-medium text-gray-900">{{ $booking->attendees }} orang</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Zoom Info --}}
        @if($booking->zoomMeeting)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="font-semibold text-blue-900 text-sm">Zoom Meeting</span>
            </div>
            <a href="{{ $booking->zoomMeeting->join_url }}" target="_blank"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Join Zoom Meeting
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
            @if($booking->zoomMeeting->password)
            <p class="text-xs text-blue-700 mt-2">Password: <strong>{{ $booking->zoomMeeting->password }}</strong></p>
            @endif
        </div>
        @endif

        {{-- Cancel link --}}
        @if($booking->cancel_token)
        <div class="text-center pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 mb-2">Perlu membatalkan?</p>
            <a href="{{ route('booking.cancel.form', $booking->cancel_token) }}"
               class="text-sm text-red-500 hover:text-red-600 font-medium">
                Batalkan Booking Ini
            </a>
        </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('booking.form') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Buat Booking Lagi
            </a>
        </div>
    </div>
</div>
@endsection
