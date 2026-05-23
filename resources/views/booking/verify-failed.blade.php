@extends('layouts.app')

@section('title', 'Konfirmasi Gagal')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Konfirmasi Gagal</h1>
        <p class="text-gray-500 mb-6">
            Link konfirmasi tidak valid, sudah kadaluarsa, atau ruang meeting sudah tidak tersedia.
        </p>
        <p class="text-sm text-gray-400 mb-8">
            Link konfirmasi berlaku selama 24 jam sejak booking dibuat.
            Jika ruang sudah dibooking orang lain, silakan buat booking baru.
        </p>
        <a href="{{ route('booking.form') }}" class="btn-primary inline-block">
            Buat Booking Baru
        </a>
    </div>
</div>
@endsection
