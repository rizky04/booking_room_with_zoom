@extends('layouts.admin')

@section('title', isset($room) ? 'Edit Ruang' : 'Tambah Ruang')

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.rooms') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Ruang
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h2 class="font-semibold text-gray-900 mb-6">{{ isset($room) ? 'Edit Ruang Meeting' : 'Tambah Ruang Meeting' }}</h2>

        @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="text-red-600 text-sm space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ isset($room) ? route('admin.rooms.update', $room->id) : route('admin.rooms.store') }}"
              method="POST" class="space-y-5">
            @csrf
            @if(isset($room)) @method('PUT') @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama Ruang <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                           value="{{ old('name', $room->name ?? '') }}"
                           placeholder="Meeting Room A"
                           class="form-input @error('name') border-red-400 @enderror">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Kode <span class="text-red-500">*</span></label>
                    <input type="text" name="code" required
                           value="{{ old('code', $room->code ?? '') }}"
                           placeholder="MRA" maxlength="10"
                           style="text-transform:uppercase"
                           class="form-input @error('code') border-red-400 @enderror">
                    @error('code') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Kapasitas (orang) <span class="text-red-500">*</span></label>
                    <input type="number" name="capacity" required min="1" max="500"
                           value="{{ old('capacity', $room->capacity ?? '') }}"
                           class="form-input @error('capacity') border-red-400 @enderror">
                    @error('capacity') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="location"
                           value="{{ old('location', $room->location ?? '') }}"
                           placeholder="Lantai 1, Gedung A"
                           class="form-input">
                </div>
            </div>

            <div>
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="2"
                          placeholder="Deskripsi singkat ruang..."
                          class="form-input">{{ old('description', $room->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" required class="form-input @error('status') border-red-400 @enderror">
                    <option value="active" @selected(old('status', $room->status ?? 'active') === 'active')>Aktif</option>
                    <option value="inactive" @selected(old('status', $room->status ?? '') === 'inactive')>Tidak Aktif</option>
                    <option value="maintenance" @selected(old('status', $room->status ?? '') === 'maintenance')>Maintenance</option>
                </select>
            </div>

            <div x-data="{
                facilities: {{ json_encode(old('facilities', isset($room) ? ($room->facilities ?? []) : [])) }},
                newItem: '',
                add() { if (this.newItem.trim()) { this.facilities.push(this.newItem.trim()); this.newItem = ''; } },
                remove(i) { this.facilities.splice(i, 1); }
            }">
                <label class="form-label">Fasilitas</label>
                <div class="flex gap-2 mb-2">
                    <input type="text" x-model="newItem" @keydown.enter.prevent="add()"
                           placeholder="Tambah fasilitas (Enter)"
                           class="form-input">
                    <button type="button" @click="add()"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors flex-shrink-0">
                        Tambah
                    </button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="(item, i) in facilities" :key="i">
                        <div class="flex items-center gap-1 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm">
                            <input type="hidden" name="facilities[]" :value="item">
                            <span x-text="item"></span>
                            <button type="button" @click="remove(i)" class="ml-1 text-blue-400 hover:text-blue-600 leading-none">×</button>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-gray-400 mt-1">cth: Proyektor, Whiteboard, AC, WiFi</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    {{ isset($room) ? 'Simpan Perubahan' : 'Tambah Ruang' }}
                </button>
                <a href="{{ route('admin.rooms') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
