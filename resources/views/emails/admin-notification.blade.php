<x-mail::message>
# [Admin] Booking Baru Dikonfirmasi

Booking baru telah dikonfirmasi oleh pengguna.

<x-mail::panel>
**{{ $booking->title }}**

👤 **Pemesan:** {{ $booking->name }} ({{ $booking->email }})
🏢 **Ruang:** {{ $booking->room->name }}
📅 **Tanggal:** {{ $booking->date->format('l, d F Y') }}
⏰ **Waktu:** {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}
👥 **Peserta:** {{ $booking->attendees }} orang
🔑 **Kode:** {{ $booking->booking_code }}
📹 **Zoom:** {{ $booking->enable_zoom ? 'Ya' : 'Tidak' }}
</x-mail::panel>

<x-mail::button :url="route('admin.bookings.show', $booking->id)">
Lihat di Admin Panel
</x-mail::button>

{{ config('app.name') }} — Admin Notification
</x-mail::message>
