<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'room_id', 'name', 'email', 'phone',
        'title', 'description', 'date', 'start_time', 'end_time',
        'attendees', 'enable_zoom', 'status', 'verification_token',
        'cancel_token', 'reschedule_token', 'email_verified_at',
        'verified_at', 'cancelled_by', 'cancel_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'enable_zoom' => 'boolean',
        'email_verified_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function zoomMeeting(): HasOne
    {
        return $this->hasOne(ZoomMeeting::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(BookingNotification::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getStartDateTimeAttribute(): string
    {
        return $this->date->format('Y-m-d') . ' ' . $this->start_time;
    }

    public function getEndDateTimeAttribute(): string
    {
        return $this->date->format('Y-m-d') . ' ' . $this->end_time;
    }

    public function getDurationMinutesAttribute(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }
}
