@extends('layouts.app')

@section('title', 'Batalkan Booking')

@section('content')
<div class="max-w-lg mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Batalkan Booking</h1>
            <p class="text-gray-500 text-sm mt-1">Apakah Anda yakin ingin membatalkan booking ini?</p>
        </div>

        {{-- Detail --}}
        <div class="bg-gray-50 rounded-xl p-4 space-y-2 mb-6 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Kode</span>
                <span class="font-semibold">{{ $booking->booking_code }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Ruang</span>
                <span class="font-semibold">{{ $booking->room->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-semibold">{{ $booking->date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Waktu</span>
                <span class="font-semibold">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Topik</span>
                <span class="font-semibold">{{ $booking->title }}</span>
            </div>
        </div>

        <form action="{{ route('booking.cancel', $booking->cancel_token) }}" method="POST">
            @csrf
            <div class="mb-5">
                <label for="reason" class="form-label">Alasan Pembatalan (Opsional)</label>
                <textarea id="reason" name="reason" rows="3"
                          placeholder="Tulis alasan pembatalan..."
                          class="form-input">{{ old('reason') }}</textarea>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('booking.form') }}"
                   class="flex-1 text-center py-2.5 px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Kembali
                </a>
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
