@extends('layouts.app')

@section('title', 'Book Meeting Room')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10"
     x-data="{
        selectedRoom: '',
        date: '',
        startTime: '',
        endTime: '',
        enableZoom: false,
        availability: null,
        checking: false,
        async checkAvailability() {
            if (!this.selectedRoom || !this.date || !this.startTime || !this.endTime) return;
            this.checking = true;
            this.availability = null;
            try {
                const params = new URLSearchParams({
                    room_id: this.selectedRoom,
                    date: this.date,
                    start_time: this.startTime,
                    end_time: this.endTime
                });
                const res = await fetch('/api/check-availability?' + params);
                this.availability = await res.json();
            } catch(e) {}
            this.checking = false;
        }
     }">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Book Meeting Room</h1>
        <p class="text-gray-500 mt-2">Isi form di bawah. Konfirmasi akan dikirim ke email Anda.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-red-700 text-sm font-semibold mb-2">Terdapat kesalahan:</p>
                <ul class="text-red-600 text-sm space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('bookings.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Nama & Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" required
                           value="{{ old('name') }}"
                           placeholder="Nama Anda"
                           class="form-input @error('name') border-red-400 @enderror">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" required
                           value="{{ old('email') }}"
                           placeholder="email@perusahaan.com"
                           class="form-input @error('email') border-red-400 @enderror">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="form-label">No. Telepon / Ext (Opsional)</label>
                <input type="tel" id="phone" name="phone"
                       value="{{ old('phone') }}"
                       placeholder="+62xxx atau ext. 123"
                       class="form-input">
            </div>

            {{-- Pilih Ruang --}}
            <div>
                <label for="room_id" class="form-label">Ruang Meeting <span class="text-red-500">*</span></label>
                <select id="room_id" name="room_id" required
                        x-model="selectedRoom"
                        @change="checkAvailability()"
                        class="form-input @error('room_id') border-red-400 @enderror">
                    <option value="">-- Pilih Ruang --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                            {{ $room->name }} (Kapasitas: {{ $room->capacity }} orang)
                            @if($room->location) — {{ $room->location }} @endif
                        </option>
                    @endforeach
                </select>
                @error('room_id') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal & Waktu --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="date" class="form-label">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" id="date" name="date" required
                           value="{{ old('date') }}"
                           min="{{ date('Y-m-d') }}"
                           x-model="date"
                           @change="checkAvailability()"
                           class="form-input @error('date') border-red-400 @enderror">
                    @error('date') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="start_time" class="form-label">Mulai <span class="text-red-500">*</span></label>
                    <input type="time" id="start_time" name="start_time" required
                           value="{{ old('start_time', '09:00') }}"
                           x-model="startTime"
                           @change="checkAvailability()"
                           class="form-input @error('start_time') border-red-400 @enderror">
                    @error('start_time') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_time" class="form-label">Selesai <span class="text-red-500">*</span></label>
                    <input type="time" id="end_time" name="end_time" required
                           value="{{ old('end_time', '10:00') }}"
                           x-model="endTime"
                           @change="checkAvailability()"
                           class="form-input @error('end_time') border-red-400 @enderror">
                    @error('end_time') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Availability checker --}}
            <div x-show="checking" class="text-sm text-gray-500 flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Mengecek ketersediaan...
            </div>
            <div x-show="availability !== null && !checking">
                <div x-show="availability && availability.available"
                     class="flex items-center gap-2 text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2.5 text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Ruang tersedia untuk waktu yang dipilih!
                </div>
                <div x-show="availability && !availability.available"
                     class="text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-2.5 text-sm">
                    <div class="flex items-center gap-2 font-medium mb-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Ruang tidak tersedia pada waktu tersebut.
                    </div>
                    <template x-if="availability && availability.schedule && availability.schedule.length > 0">
                        <div>
                            <p class="text-xs mb-1 font-medium">Jadwal yang sudah dibooking:</p>
                            <template x-for="s in availability.schedule" :key="s.start_time">
                                <p class="text-xs" x-text="s.start_time + ' – ' + s.end_time + ' (' + s.title + ')'"></p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Jumlah Peserta --}}
            <div>
                <label for="attendees" class="form-label">Jumlah Peserta</label>
                <input type="number" id="attendees" name="attendees" min="1" max="100"
                       value="{{ old('attendees', 1) }}"
                       class="form-input w-32">
            </div>

            {{-- Topik & Deskripsi --}}
            <div>
                <label for="title" class="form-label">Topik Meeting <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required
                       value="{{ old('title') }}"
                       placeholder="cth: Project Kickoff, Team Standup, Review Bulanan"
                       class="form-input @error('title') border-red-400 @enderror">
                @error('title') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="form-label">Keterangan (Opsional)</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Detail tambahan tentang meeting..."
                          class="form-input @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
            </div>

            {{-- Zoom --}}
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl p-4"
                 x-data>
                <input type="checkbox" id="enable_zoom" name="enable_zoom" value="1"
                       @checked(old('enable_zoom'))
                       x-model="enableZoom"
                       class="mt-0.5 w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                <div>
                    <label for="enable_zoom" class="text-sm font-semibold text-gray-900 cursor-pointer">
                        Buat link Zoom Meeting otomatis
                    </label>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Link Zoom akan disertakan dalam email konfirmasi jika tersedia.
                    </p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3 px-6 rounded-xl transition-colors text-base">
                    Buat Booking
                </button>
            </div>
        </form>

        {{-- Info --}}
        <div class="mt-8 pt-6 border-t border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Informasi Penting</h3>
            <ul class="text-sm text-gray-500 space-y-1.5">
                <li class="flex items-start gap-2">
                    <span class="text-blue-500 mt-0.5">•</span>
                    Setelah submit, cek email Anda dan klik link konfirmasi untuk mengaktifkan booking.
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-500 mt-0.5">•</span>
                    Pengingat akan dikirim 24 jam dan 1 jam sebelum meeting.
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-500 mt-0.5">•</span>
                    Booking dapat dibatalkan melalui link di email konfirmasi.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
