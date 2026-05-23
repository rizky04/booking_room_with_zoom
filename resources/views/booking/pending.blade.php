@extends('layouts.app')

@section('title', 'Cek Email Anda')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10">

        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Cek Email Anda!</h1>
        <p class="text-gray-500 mb-6">
            Kami telah mengirimkan link konfirmasi ke
            <strong class="text-gray-700">{{ $booking->email }}</strong>.
            Klik link tersebut untuk mengkonfirmasi booking Anda.
        </p>

        <div class="bg-gray-50 rounded-xl p-4 text-left space-y-2 mb-6">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Kode Booking</span>
                <span class="font-semibold text-gray-900">{{ $booking->booking_code }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Ruang</span>
                <span class="font-semibold text-gray-900">{{ $booking->room->name }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-semibold text-gray-900">{{ $booking->date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Waktu</span>
                <span class="font-semibold text-gray-900">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}</span>
            </div>
        </div>

        <p class="text-xs text-gray-400">
            Link konfirmasi berlaku selama 24 jam. Jika tidak menerima email, cek folder Spam.
        </p>

        <div class="mt-6">
            <a href="{{ route('booking.form') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                ← Kembali ke Form Booking
            </a>
        </div>
    </div>
</div>
@endsection
