@extends('layouts.admin')

@section('title', 'Buat Booking')

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.bookings') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Booking
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <div class="mb-6">
            <h2 class="font-semibold text-gray-900 text-lg">Buat Booking Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Booking yang dibuat admin langsung berstatus <span class="font-medium text-green-600">Confirmed</span> dan email konfirmasi dikirim otomatis.</p>
        </div>

        @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="text-red-600 text-sm space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.bookings.store') }}" method="POST" class="space-y-5"
              x-data="{
                  enableZoom: {{ old('enable_zoom') ? 'true' : 'false' }},
                  checking: false,
                  available: null,
                  checkAvailability() {
                      const roomId = document.getElementById('room_id').value;
                      const date = document.getElementById('date').value;
                      const start = document.getElementById('start_time').value;
                      const end = document.getElementById('end_time').value;
                      if (!roomId || !date || !start || !end) return;
                      this.checking = true;
                      this.available = null;
                      fetch(`/api/check-availability?room_id=${roomId}&date=${date}&start_time=${start}&end_time=${end}`)
                          .then(r => r.json())
                          .then(data => { this.available = data.available; this.checking = false; });
                  }
              }">
            @csrf

            {{-- Informasi Pemesan --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-100">Informasi Pemesan</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                               value="{{ old('name') }}"
                               placeholder="Nama lengkap"
                               class="form-input @error('name') border-red-400 @enderror">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required
                               value="{{ old('email') }}"
                               placeholder="email@perusahaan.com"
                               class="form-input @error('email') border-red-400 @enderror">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label class="form-label">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone"
                           value="{{ old('phone') }}"
                           placeholder="08xxxxxxxxxx"
                           class="form-input">
                </div>
            </div>

            {{-- Detail Meeting --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-100">Detail Meeting</h3>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Topik Meeting <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required
                               value="{{ old('title') }}"
                               placeholder="Rapat Mingguan, Review Project, dll"
                               class="form-input @error('title') border-red-400 @enderror">
                        @error('title') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Deskripsi / Agenda</label>
                        <textarea name="description" rows="3"
                                  placeholder="Agenda meeting, hal yang akan dibahas, dll"
                                  class="form-input">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Jumlah Peserta <span class="text-red-500">*</span></label>
                        <input type="number" name="attendees" required min="1" max="500"
                               value="{{ old('attendees', 1) }}"
                               class="form-input w-32 @error('attendees') border-red-400 @enderror">
                        @error('attendees') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Jadwal & Ruang --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-100">Jadwal & Ruang</h3>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Ruang Meeting <span class="text-red-500">*</span></label>
                        <select name="room_id" id="room_id" required
                                @change="checkAvailability()"
                                class="form-input @error('room_id') border-red-400 @enderror">
                            <option value="">-- Pilih Ruang --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                                    {{ $room->name }}
                                    @if($room->location) ({{ $room->location }}) @endif
                                    — Kapasitas {{ $room->capacity }} orang
                                </option>
                            @endforeach
                        </select>
                        @error('room_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="date" id="date" required
                               value="{{ old('date') }}"
                               min="{{ now()->toDateString() }}"
                               @change="checkAvailability()"
                               class="form-input @error('date') border-red-400 @enderror">
                        @error('date') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Waktu Mulai <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time" id="start_time" required
                                   value="{{ old('start_time') }}"
                                   @change="checkAvailability()"
                                   class="form-input @error('start_time') border-red-400 @enderror">
                            @error('start_time') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Waktu Selesai <span class="text-red-500">*</span></label>
                            <input type="time" name="end_time" id="end_time" required
                                   value="{{ old('end_time') }}"
                                   @change="checkAvailability()"
                                   class="form-input @error('end_time') border-red-400 @enderror">
                            @error('end_time') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Status Ketersediaan --}}
                    <div x-show="checking || available !== null" x-cloak>
                        <div x-show="checking" class="text-sm text-gray-500 flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Memeriksa ketersediaan...
                        </div>
                        <div x-show="!checking && available === true"
                             class="text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg px-3 py-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Ruang tersedia pada waktu ini
                        </div>
                        <div x-show="!checking && available === false"
                             class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Ruang sudah dibooking pada waktu ini
                        </div>
                    </div>
                </div>
            </div>

            {{-- Zoom --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="enable_zoom" value="1"
                           x-model="enableZoom"
                           {{ old('enable_zoom') ? 'checked' : '' }}
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600">
                    <div>
                        <div class="font-medium text-gray-900 text-sm">Buat Zoom Meeting Otomatis</div>
                        <div class="text-xs text-gray-500 mt-0.5">Link Zoom akan dibuat dan disertakan dalam email konfirmasi</div>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    Buat Booking
                </button>
                <a href="{{ route('admin.bookings') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
