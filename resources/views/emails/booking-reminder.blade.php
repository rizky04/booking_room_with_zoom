<x-mail::message>
# Pengingat Meeting {{ $reminderType === 'reminder_1h' ? '1 Jam Lagi' : '24 Jam Lagi' }} ⏰

Halo **{{ $booking->name }}**,

Meeting Anda akan dimulai dalam **{{ $reminderType === 'reminder_1h' ? '1 jam' : '24 jam' }} lagi**!

<x-mail::panel>
**{{ $booking->title }}**

🏢 **Ruang:** {{ $booking->room->name }} {{ $booking->room->location ? '(' . $booking->room->location . ')' : '' }}
📅 **Tanggal:** {{ $booking->date->format('l, d F Y') }}
⏰ **Waktu:** {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}
🔑 **Kode Booking:** {{ $booking->booking_code }}
</x-mail::panel>

@if($booking->zoomMeeting)
<x-mail::button :url="$booking->zoomMeeting->join_url">
Join Zoom Meeting
</x-mail::button>
@if($booking->zoomMeeting->password)

**Password Zoom:** {{ $booking->zoomMeeting->password }}
@endif
@endif

Salam,<br>
Tim {{ config('app.name') }}
</x-mail::message>
