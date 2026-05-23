<x-mail::message>
# Konfirmasi Booking Anda

Halo **{{ $booking->name }}**,

Terima kasih telah membuat booking di **{{ config('app.name') }}**. Silakan klik tombol di bawah untuk mengkonfirmasi booking Anda.

<x-mail::panel>
**{{ $booking->title }}**

🏢 **Ruang:** {{ $booking->room->name }} {{ $booking->room->location ? '(' . $booking->room->location . ')' : '' }}
📅 **Tanggal:** {{ $booking->date->format('l, d F Y') }}
⏰ **Waktu:** {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}
🔑 **Kode Booking:** {{ $booking->booking_code }}
</x-mail::panel>

<x-mail::button :url="route('booking.verify', $booking->verification_token)">
✅ Konfirmasi Booking
</x-mail::button>

**Penting:** Link konfirmasi ini berlaku selama **24 jam**. Setelah dikonfirmasi, booking Anda akan aktif dan Anda akan menerima email berisi detail lengkap.

Jika Anda tidak membuat booking ini, abaikan email ini.

Salam,<br>
Tim {{ config('app.name') }}
</x-mail::message>
