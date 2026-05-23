@extends('layouts.app')

@section('title', 'Book Meeting Room')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10"
     x-data="{
        selectedRoom: '{{ old('room_id', '') }}',
        selectedRoomName: '',
        date: '{{ old('date', '') }}',
        startTime: '{{ old('start_time', '') }}',
        endTime: '{{ old('end_time', '') }}',
        enableZoom: {{ old('enable_zoom') ? 'true' : 'false' }},

        /* availability check */
        availability: null,
        checking: false,

        /* calendar */
        calYear: new Date().getFullYear(),
        calMonth: new Date().getMonth(),
        busyDays: {},
        calLoading: false,

        /* time slots */
        timeSlots: [],
        slotsLoading: false,

        get calMonthLabel() {
            return new Date(this.calYear, this.calMonth, 1)
                .toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        },
        get calDays() {
            const year = this.calYear, month = this.calMonth;
            const firstDay = new Date(year, month, 1).getDay();
            const offset = firstDay === 0 ? 6 : firstDay - 1; /* Mon=0 */
            const total = new Date(year, month + 1, 0).getDate();
            const days = [];
            for (let i = 0; i < offset; i++) days.push(null);
            for (let d = 1; d <= total; d++) days.push(d);
            return days;
        },
        calDateStr(d) {
            if (!d) return '';
            return this.calYear + '-' +
                String(this.calMonth + 1).padStart(2,'0') + '-' +
                String(d).padStart(2,'0');
        },
        isPast(d) {
            if (!d) return false;
            return this.calDateStr(d) < new Date().toISOString().slice(0,10);
        },
        isToday(d) {
            return d && this.calDateStr(d) === new Date().toISOString().slice(0,10);
        },
        isSelected(d) {
            return d && this.calDateStr(d) === this.date;
        },
        dayStatus(d) {
            if (!d) return 'empty';
            const str = this.calDateStr(d);
            if (!this.busyDays[str]) return 'free';
            /* check if fully blocked (all 07-21 is booked) — simplified: just show partial */
            return 'busy';
        },

        async calPrev() {
            if (this.calMonth === 0) { this.calYear--; this.calMonth = 11; }
            else this.calMonth--;
            await this.loadMonthly();
        },
        async calNext() {
            if (this.calMonth === 11) { this.calYear++; this.calMonth = 0; }
            else this.calMonth++;
            await this.loadMonthly();
        },

        async loadMonthly() {
            if (!this.selectedRoom) return;
            this.calLoading = true;
            try {
                const res = await fetch('/api/room-monthly?room_id=' + this.selectedRoom +
                    '&year=' + this.calYear + '&month=' + (this.calMonth + 1));
                const data = await res.json();
                this.busyDays = data.busy_days || {};
            } catch(e) {}
            this.calLoading = false;
        },

        async pickDate(d) {
            if (!d || this.isPast(d)) return;
            this.date = this.calDateStr(d);
            document.getElementById('date').value = this.date;
            await this.loadTimeSlots();
            await this.checkAvailability();
        },

        async loadTimeSlots() {
            if (!this.selectedRoom || !this.date) return;
            this.slotsLoading = true;
            this.timeSlots = [];
            try {
                const res = await fetch('/api/room-schedule?room_id=' + this.selectedRoom + '&date=' + this.date);
                const data = await res.json();
                const schedule = data.schedule || [];
                const slots = [];
                for (let h = 7; h < 21; h++) {
                    for (let m = 0; m < 60; m += 30) {
                        const time = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                        const timeEnd = m === 30
                            ? String(h+1).padStart(2,'0') + ':00'
                            : String(h).padStart(2,'00') + ':30';
                        const booking = schedule.find(b => b.start_time <= time && b.end_time > time);
                        slots.push({ time, timeEnd, booking: booking || null });
                    }
                }
                this.timeSlots = slots;
            } catch(e) {}
            this.slotsLoading = false;
        },

        selectSlot(slot) {
            if (slot.booking) return;
            this.startTime = slot.time;
            document.getElementById('start_time').value = slot.time;
            /* auto set end +1h */
            const [h, m] = slot.time.split(':').map(Number);
            const eh = Math.min(h + 1, 21);
            const endVal = String(eh).padStart(2,'0') + ':' + String(m).padStart(2,'0');
            this.endTime = endVal;
            document.getElementById('end_time').value = endVal;
            this.checkAvailability();
        },

        async onRoomChange() {
            this.busyDays = {};
            this.timeSlots = [];
            const sel = document.getElementById('room_id');
            this.selectedRoomName = sel.options[sel.selectedIndex]?.text || '';
            await this.loadMonthly();
            if (this.date) await this.loadTimeSlots();
            await this.checkAvailability();
        },

        async checkAvailability() {
            if (!this.selectedRoom || !this.date || !this.startTime || !this.endTime) return;
            this.checking = true; this.availability = null;
            try {
                const params = new URLSearchParams({
                    room_id: this.selectedRoom, date: this.date,
                    start_time: this.startTime, end_time: this.endTime
                });
                const res = await fetch('/api/check-availability?' + params);
                this.availability = await res.json();
            } catch(e) {}
            this.checking = false;
        },

        init() {
            if (this.selectedRoom) {
                this.loadMonthly();
                if (this.date) this.loadTimeSlots();
            }
            /* sync calendar month to selected date if any */
            if (this.date) {
                const d = new Date(this.date);
                this.calYear = d.getFullYear();
                this.calMonth = d.getMonth();
            }
        }
     }">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Book Meeting Room</h1>
        <p class="text-gray-500 mt-2">Isi form di bawah. Konfirmasi akan dikirim ke email Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- ── FORM KIRI ── --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                @if($errors->any())
                    <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-700 text-sm font-semibold mb-1">Terdapat kesalahan:</p>
                        <ul class="text-red-600 text-sm space-y-1 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('bookings.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                   placeholder="Nama Anda"
                                   class="form-input @error('name') border-red-400 @enderror">
                            @error('name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                   placeholder="email@perusahaan.com"
                                   class="form-input @error('email') border-red-400 @enderror">
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">No. Telepon (Opsional)</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               placeholder="+62xxx" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Ruang Meeting <span class="text-red-500">*</span></label>
                        <select id="room_id" name="room_id" required
                                x-model="selectedRoom"
                                @change="onRoomChange()"
                                class="form-input @error('room_id') border-red-400 @enderror">
                            <option value="">-- Pilih Ruang --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                                    {{ $room->name }} ({{ $room->capacity }} orang)@if($room->location) — {{ $room->location }}@endif
                                </option>
                            @endforeach
                        </select>
                        @error('room_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" id="date" name="date" required
                                   value="{{ old('date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   x-model="date"
                                   @change="loadTimeSlots(); checkAvailability()"
                                   class="form-input @error('date') border-red-400 @enderror">
                            @error('date') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Mulai <span class="text-red-500">*</span></label>
                            <input type="time" id="start_time" name="start_time" required
                                   value="{{ old('start_time', '09:00') }}"
                                   x-model="startTime"
                                   @change="checkAvailability()"
                                   class="form-input @error('start_time') border-red-400 @enderror">
                            @error('start_time') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Selesai <span class="text-red-500">*</span></label>
                            <input type="time" id="end_time" name="end_time" required
                                   value="{{ old('end_time', '10:00') }}"
                                   x-model="endTime"
                                   @change="checkAvailability()"
                                   class="form-input @error('end_time') border-red-400 @enderror">
                            @error('end_time') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Availability status --}}
                    <div x-show="checking" class="text-sm text-gray-500 flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Mengecek ketersediaan...
                    </div>
                    <div x-show="availability !== null && !checking" x-cloak>
                        <div x-show="availability && availability.available"
                             class="flex items-center gap-2 text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2.5 text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Ruang tersedia untuk waktu yang dipilih!
                        </div>
                        <div x-show="availability && !availability.available"
                             class="text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-2.5 text-sm">
                            <div class="flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Ruang tidak tersedia pada waktu tersebut.
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Topik Meeting <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required value="{{ old('title') }}"
                                   placeholder="Project Kickoff, Rapat Tim..."
                                   class="form-input @error('title') border-red-400 @enderror">
                            @error('title') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Jumlah Peserta</label>
                            <input type="number" name="attendees" min="1" max="100"
                                   value="{{ old('attendees', 1) }}" class="form-input">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea name="description" rows="2"
                                  placeholder="Detail tambahan..."
                                  class="form-input">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl p-4">
                        <input type="checkbox" id="enable_zoom" name="enable_zoom" value="1"
                               x-model="enableZoom"
                               @checked(old('enable_zoom'))
                               class="mt-0.5 w-4 h-4 text-blue-600 rounded border-gray-300">
                        <div>
                            <label for="enable_zoom" class="text-sm font-semibold text-gray-900 cursor-pointer">
                                Buat link Zoom Meeting otomatis
                            </label>
                            <p class="text-xs text-gray-500 mt-0.5">Link Zoom akan disertakan dalam email konfirmasi.</p>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-colors">
                        Buat Booking
                    </button>
                </form>
            </div>
        </div>

        {{-- ── KALENDER KANAN ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Placeholder saat belum pilih ruang --}}
            <div x-show="!selectedRoom" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm">Pilih ruang untuk melihat ketersediaan kalender</p>
            </div>

            {{-- Kalender Bulan --}}
            <div x-show="selectedRoom" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="flex items-center justify-between px-4 py-3 bg-blue-600 text-white">
                    <button type="button" @click="calPrev()"
                            class="w-7 h-7 rounded-full hover:bg-blue-500 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <div class="text-center">
                        <div class="font-semibold text-sm" x-text="calMonthLabel"></div>
                        <div class="text-xs text-blue-200" x-show="selectedRoomName" x-text="selectedRoomName"></div>
                    </div>
                    <button type="button" @click="calNext()"
                            class="w-7 h-7 rounded-full hover:bg-blue-500 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- Day labels --}}
                <div class="grid grid-cols-7 border-b border-gray-100">
                    <template x-for="day in ['Sen','Sel','Rab','Kam','Jum','Sab','Min']">
                        <div class="text-center text-xs font-semibold text-gray-400 py-2" x-text="day"></div>
                    </template>
                </div>

                {{-- Calendar grid --}}
                <div class="relative">
                    <div x-show="calLoading" class="absolute inset-0 bg-white/70 flex items-center justify-center z-10">
                        <svg class="animate-spin w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                    <div class="grid grid-cols-7">
                        <template x-for="(d, i) in calDays" :key="i">
                            <button type="button"
                                    @click="d && !isPast(d) && pickDate(d)"
                                    :class="{
                                        'opacity-0 pointer-events-none': !d,
                                        'cursor-not-allowed opacity-40': d && isPast(d),
                                        'cursor-pointer': d && !isPast(d),
                                        'bg-blue-600 text-white rounded-full': isSelected(d),
                                        'bg-blue-50 text-blue-700 font-bold': isToday(d) && !isSelected(d),
                                        'hover:bg-gray-100': d && !isPast(d) && !isSelected(d),
                                    }"
                                    class="flex flex-col items-center justify-center py-1.5 text-xs relative transition-colors">
                                <span x-text="d || ''" class="font-medium leading-none mb-1"></span>
                                {{-- dot indicator --}}
                                <template x-if="d && !isPast(d) && busyDays[calDateStr(d)]">
                                    <span :class="isSelected(d) ? 'bg-white' : 'bg-red-400'"
                                          class="w-1 h-1 rounded-full"></span>
                                </template>
                                <template x-if="d && !isPast(d) && !busyDays[calDateStr(d)] && selectedRoom">
                                    <span :class="isSelected(d) ? 'bg-white' : 'bg-green-400'"
                                          class="w-1 h-1 rounded-full"></span>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="flex items-center gap-4 px-4 py-2.5 border-t border-gray-100 bg-gray-50 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span> Tersedia</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span> Ada booking</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-600 inline-block"></span> Dipilih</span>
                </div>
            </div>

            {{-- Time Slot Grid --}}
            <div x-show="selectedRoom && date" x-cloak class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Slot Waktu</div>
                        <div class="text-xs text-gray-400" x-text="date ? new Date(date + 'T00:00:00').toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long'}) : ''"></div>
                    </div>
                    <div x-show="slotsLoading">
                        <svg class="animate-spin w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                </div>

                <div class="p-3 max-h-72 overflow-y-auto">
                    <div x-show="!slotsLoading && timeSlots.length === 0" class="text-center text-gray-400 text-sm py-4">
                        Pilih tanggal untuk melihat slot waktu
                    </div>

                    {{-- Group slots by hour --}}
                    <div class="space-y-1">
                        <template x-for="(slot, idx) in timeSlots" :key="idx">
                            <div class="flex items-center gap-2">
                                {{-- time label only on :00 --}}
                                <div class="w-10 text-right flex-shrink-0">
                                    <span x-show="slot.time.endsWith(':00')"
                                          class="text-xs font-semibold text-gray-400"
                                          x-text="slot.time"></span>
                                </div>
                                <button type="button"
                                        @click="selectSlot(slot)"
                                        :disabled="!!slot.booking"
                                        :class="{
                                            'bg-red-100 border-red-200 text-red-700 cursor-not-allowed': slot.booking,
                                            'bg-green-50 border-green-200 text-green-700 hover:bg-green-100 cursor-pointer': !slot.booking,
                                            'ring-2 ring-blue-500': startTime === slot.time && !slot.booking,
                                        }"
                                        class="flex-1 text-left px-3 py-1.5 rounded-lg border text-xs transition-all flex items-center justify-between">
                                    <span x-text="slot.time + ' – ' + slot.timeEnd"></span>
                                    <span x-show="slot.booking"
                                          class="font-medium truncate max-w-[120px] ml-2"
                                          x-text="slot.booking ? slot.booking.title : ''"></span>
                                    <span x-show="!slot.booking" class="text-green-600">Tersedia</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center gap-4 px-4 py-2.5 border-t border-gray-100 bg-gray-50 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-100 border border-green-200 inline-block"></span> Tersedia (klik untuk pilih)</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-100 border border-red-200 inline-block"></span> Sudah dipesan</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
