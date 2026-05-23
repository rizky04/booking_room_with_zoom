<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingNotification extends Model
{
    protected $fillable = [
        'booking_id', 'type', 'recipient_email',
        'status', 'sent_at', 'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function booking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
