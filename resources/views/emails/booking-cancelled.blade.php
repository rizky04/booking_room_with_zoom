<x-mail::message>
# Booking Dibatalkan

Halo **{{ $booking->name }}**,

Booking Anda dengan kode **{{ $booking->booking_code }}** telah dibatalkan.

<x-mail::panel>
**{{ $booking->title }}**

🏢 **Ruang:** {{ $booking->room->name }}
📅 **Tanggal:** {{ $booking->date->format('l, d F Y') }}
⏰ **Waktu:** {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}
@if($booking->cancel_reason)
❌ **Alasan:** {{ $booking->cancel_reason }}
@endif
</x-mail::panel>

Jika Anda masih memerlukan ruang meeting, silakan buat booking baru.

<x-mail::button :url="route('booking.form')">
Buat Booking Baru
</x-mail::button>

Salam,<br>
Tim {{ config('app.name') }}
</x-mail::message>
