<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    protected $fillable = [
        'booking_id', 'account_index', 'zoom_meeting_id', 'zoom_uuid', 'host_id',
        'topic', 'join_url', 'start_url', 'password',
        'host_email', 'duration', 'start_time', 'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function booking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
