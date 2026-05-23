<x-mail::message>
# Booking Dikonfirmasi! 🎉

Halo **{{ $booking->name }}**,

Booking Anda telah **berhasil dikonfirmasi**. Sampai jumpa di meeting!

<x-mail::panel>
**{{ $booking->title }}**

🏢 **Ruang:** {{ $booking->room->name }} {{ $booking->room->location ? '(' . $booking->room->location . ')' : '' }}
📅 **Tanggal:** {{ $booking->date->format('l, d F Y') }}
⏰ **Waktu:** {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }} ({{ $booking->duration_minutes }} menit)
🔑 **Kode Booking:** {{ $booking->booking_code }}
</x-mail::panel>

@if($booking->zoomMeeting)
<x-mail::panel>
**Zoom Meeting**

🔗 [Bergabung ke Rapat Zoom]({{ $booking->zoomMeeting->join_url }})

📋 **ID Rapat:** {{ $booking->zoomMeeting->zoom_meeting_id }}
@if($booking->zoomMeeting->password)
🔒 **Kode Sandi:** {{ $booking->zoomMeeting->password }}
@endif
</x-mail::panel>
@endif

Anda akan menerima pengingat **24 jam** dan **1 jam** sebelum meeting dimulai.

Perlu membatalkan? Klik link di bawah:

<x-mail::button :url="route('booking.cancel.form', $booking->cancel_token)" color="red">
Batalkan Booking
</x-mail::button>

Salam,<br>
Tim {{ config('app.name') }}
</x-mail::message>
