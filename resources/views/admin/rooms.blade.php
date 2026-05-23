@extends('layouts.admin')

@section('title', 'Ruang Meeting')

@section('content')

<div class="flex items-center justify-between mb-5">
    <div></div>
    <a href="{{ route('admin.rooms.create') }}" class="btn-primary text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Ruang
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($rooms as $room)
    <div class="card overflow-hidden">
        <div class="p-5">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $room->name }}</h3>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $room->code }}</p>
                </div>
                <span class="{{ $room->status === 'active' ? 'badge-confirmed' : ($room->status === 'maintenance' ? 'badge-pending' : 'badge-cancelled') }}">
                    {{ ucfirst($room->status) }}
                </span>
            </div>

            <div class="space-y-1.5 text-sm text-gray-600 mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Kapasitas: {{ $room->capacity }} orang
                </div>
                @if($room->location)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $room->location }}
                </div>
                @endif
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $room->bookings_count }} booking aktif
                </div>
            </div>

            @if($room->facilities)
            <div class="flex flex-wrap gap-1 mb-4">
                @foreach($room->facilities as $f)
                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs">{{ $f }}</span>
                @endforeach
            </div>
            @endif

            <div class="flex gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.rooms.edit', $room->id) }}"
                   class="flex-1 text-center py-1.5 text-sm border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                    Edit
                </a>
                <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST"
                      onsubmit="return confirm('Hapus ruang {{ $room->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="py-1.5 px-3 text-sm border border-red-200 rounded-lg text-red-600 hover:bg-red-50 transition-colors font-medium">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 card p-12 text-center text-gray-400">
        Belum ada ruang meeting. <a href="{{ route('admin.rooms.create') }}" class="text-blue-600">Tambah sekarang</a>.
    </div>
    @endforelse
</div>
@endsection
