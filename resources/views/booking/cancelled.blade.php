@extends('layouts.app')

@section('title', 'Booking Dibatalkan')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Booking Dibatalkan</h1>
        <p class="text-gray-500 mb-2">
            Booking <strong>{{ $booking->booking_code }}</strong> telah berhasil dibatalkan.
        </p>
        <p class="text-sm text-gray-400 mb-8">Email konfirmasi pembatalan telah dikirim ke {{ $booking->email }}.</p>
        <a href="{{ route('booking.form') }}" class="btn-primary inline-block">
            Buat Booking Baru
        </a>
    </div>
</div>
@endsection
