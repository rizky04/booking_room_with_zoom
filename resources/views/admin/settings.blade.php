@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- App Info --}}
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Informasi Sistem</h3>
        <div class="divide-y divide-gray-100 text-sm">
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Aplikasi</span>
                <span class="font-medium text-gray-900">{{ config('app.name') }}</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">URL</span>
                <span class="font-medium text-gray-900">{{ config('app.url') }}</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Environment</span>
                <span class="font-medium text-gray-900">{{ config('app.env') }}</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Laravel</span>
                <span class="font-medium text-gray-900">{{ app()->version() }}</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">PHP</span>
                <span class="font-medium text-gray-900">{{ PHP_VERSION }}</span>
            </div>
        </div>
    </div>

    {{-- Booking Rules --}}
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Aturan Booking</h3>
        <div class="divide-y divide-gray-100 text-sm">
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Maks. Pemesanan ke Depan</span>
                <span class="font-medium text-gray-900">{{ config('booking.booking_rules.max_advance_days') }} hari</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Durasi Minimum</span>
                <span class="font-medium text-gray-900">{{ config('booking.booking_rules.min_duration_min') }} menit</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Durasi Maksimum</span>
                <span class="font-medium text-gray-900">{{ config('booking.booking_rules.max_duration_hours') }} jam</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Jam Operasional</span>
                <span class="font-medium text-gray-900">
                    {{ config('booking.booking_rules.operating_hours.start') }} –
                    {{ config('booking.booking_rules.operating_hours.end') }}
                </span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Link Verifikasi Kadaluarsa</span>
                <span class="font-medium text-gray-900">{{ config('booking.verification_token_expiry') }} jam</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Email Admin</span>
                <span class="font-medium text-gray-900">{{ config('booking.admin_email') }}</span>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3">Ubah pengaturan ini melalui file <code class="bg-gray-100 px-1 rounded">.env</code> dan <code class="bg-gray-100 px-1 rounded">config/booking.php</code>.</p>
    </div>

    {{-- Zoom Config --}}
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Konfigurasi Zoom</h3>
        <div class="divide-y divide-gray-100 text-sm">
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Status</span>
                @if(config('zoom.client_id') && config('zoom.account_id'))
                    <span class="badge-confirmed">Terkonfigurasi</span>
                @else
                    <span class="badge-cancelled">Belum dikonfigurasi</span>
                @endif
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Account ID</span>
                <span class="font-mono text-gray-900">{{ config('zoom.account_id') ? '••••' . substr(config('zoom.account_id'), -4) : '—' }}</span>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3">Konfigurasi Zoom API melalui file <code class="bg-gray-100 px-1 rounded">.env</code>.</p>
    </div>

    {{-- Admin Account --}}
    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Akun Admin</h3>
        <div class="divide-y divide-gray-100 text-sm">
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Nama</span>
                <span class="font-medium text-gray-900">{{ auth('admin')->user()->name }}</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Email</span>
                <span class="font-medium text-gray-900">{{ auth('admin')->user()->email }}</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Role</span>
                <span class="font-medium text-gray-900">{{ auth('admin')->user()->role }}</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Login Terakhir</span>
                <span class="font-medium text-gray-900">
                    {{ auth('admin')->user()->last_login_at?->format('d M Y H:i') ?? '—' }}
                </span>
            </div>
        </div>
    </div>

</div>
@endsection
