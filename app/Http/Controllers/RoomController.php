<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::withCount(['bookings' => fn($q) => $q->whereIn('status', ['pending', 'confirmed'])])
            ->orderBy('name')
            ->get();

        return view('admin.rooms', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms-form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:10', 'unique:rooms,code'],
            'capacity'    => ['required', 'integer', 'min:1', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
            'location'    => ['nullable', 'string', 'max:100'],
            'facilities'  => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:50'],
            'status'      => ['required', 'in:active,inactive,maintenance'],
        ]);

        Room::create($data);

        return redirect()->route('admin.rooms')->with('success', 'Ruang meeting berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $room = Room::findOrFail($id);
        return view('admin.rooms-form', compact('room'));
    }

    public function update(Request $request, int $id)
    {
        $room = Room::findOrFail($id);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:10', "unique:rooms,code,{$id}"],
            'capacity'    => ['required', 'integer', 'min:1', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
            'location'    => ['nullable', 'string', 'max:100'],
            'facilities'  => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:50'],
            'status'      => ['required', 'in:active,inactive,maintenance'],
        ]);

        $room->update($data);

        return redirect()->route('admin.rooms')->with('success', 'Ruang meeting berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $room = Room::findOrFail($id);

        $activeBookings = $room->bookings()->whereIn('status', ['pending', 'confirmed'])->count();
        if ($activeBookings > 0) {
            return back()->with('error', 'Tidak bisa menghapus ruang yang masih memiliki booking aktif.');
        }

        $room->delete();

        return redirect()->route('admin.rooms')->with('success', 'Ruang meeting berhasil dihapus.');
    }
}
