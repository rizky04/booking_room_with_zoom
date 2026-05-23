@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')

{{-- Filter --}}
<div class="card p-4 mb-5">
    <form method="GET" action="{{ route('admin.reports') }}" class="flex gap-3 items-end">
        <div>
            <label class="form-label text-xs">Tahun</label>
            <select name="year" class="form-input text-sm py-2">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Bulan</label>
            <select name="month" class="form-input text-sm py-2">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" @selected($month == $m)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary text-sm py-2">Tampilkan</button>
    </form>
</div>

{{-- Summary Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="card p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Booking</div>
        <div class="text-3xl font-bold text-gray-900">{{ $totalThisMonth }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Dikonfirmasi</div>
        <div class="text-3xl font-bold text-green-600">{{ $confirmedMonth }}</div>
    </div>
    <div class="card p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Dibatalkan</div>
        <div class="text-3xl font-bold text-red-500">{{ $cancelledMonth }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Booking per Room --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Booking per Ruang</h3>
        </div>
        @if($roomStats->isEmpty())
        <div class="px-5 py-8 text-center text-gray-400 text-sm">Tidak ada data</div>
        @else
        <div class="divide-y divide-gray-100">
            @foreach($roomStats as $stat)
            <div class="flex items-center justify-between px-5 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $stat->room->name }}</p>
                    <p class="text-xs text-gray-400">{{ $stat->room->location }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900">{{ $stat->total_bookings }}</p>
                    <p class="text-xs text-gray-400">booking</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Booking harian --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Booking Harian</h3>
        </div>
        @if($monthlyStats->isEmpty())
        <div class="px-5 py-8 text-center text-gray-400 text-sm">Tidak ada data</div>
        @else
        <div class="overflow-y-auto max-h-80">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500">Tanggal</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold text-gray-500">Total</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold text-green-600">Confirmed</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold text-yellow-600">Pending</th>
                        <th class="px-5 py-2.5 text-center text-xs font-semibold text-red-500">Cancelled</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($monthlyStats as $stat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-2.5 text-gray-900">
                            {{ \Carbon\Carbon::parse($stat->booking_date)->format('d M Y') }}
                        </td>
                        <td class="px-5 py-2.5 text-center font-semibold text-gray-900">{{ $stat->total }}</td>
                        <td class="px-5 py-2.5 text-center text-green-600">{{ $stat->confirmed }}</td>
                        <td class="px-5 py-2.5 text-center text-yellow-600">{{ $stat->pending }}</td>
                        <td class="px-5 py-2.5 text-center text-red-500">{{ $stat->cancelled }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
