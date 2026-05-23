<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxDays = config('booking.booking_rules.max_advance_days', 90);

        return [
            'name'        => ['required', 'string', 'max:100'],
            'email'       => ['required', 'email', 'max:150'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'room_id'     => ['required', 'exists:rooms,id'],
            'date'        => ['required', 'date', 'after_or_equal:today', 'before_or_equal:' . now()->addDays($maxDays)->format('Y-m-d')],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'attendees'   => ['nullable', 'integer', 'min:1', 'max:100'],
            'enable_zoom' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Nama harus diisi.',
            'email.required'      => 'Email harus diisi.',
            'email.email'         => 'Format email tidak valid.',
            'room_id.required'    => 'Pilih ruang meeting.',
            'room_id.exists'      => 'Ruang meeting tidak ditemukan.',
            'date.required'       => 'Tanggal harus diisi.',
            'date.after_or_equal' => 'Tanggal tidak boleh kurang dari hari ini.',
            'start_time.required' => 'Waktu mulai harus diisi.',
            'end_time.required'   => 'Waktu selesai harus diisi.',
            'end_time.after'      => 'Waktu selesai harus setelah waktu mulai.',
            'title.required'      => 'Topik meeting harus diisi.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'enable_zoom' => $this->has('enable_zoom') ? true : false,
        ]);
    }
}
