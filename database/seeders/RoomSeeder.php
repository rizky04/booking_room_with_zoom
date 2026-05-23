<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'name'        => 'Meeting Room A',
                'code'        => 'MRA',
                'capacity'    => 10,
                'description' => 'Ruang meeting standar dengan proyektor dan whiteboard.',
                'location'    => 'Lantai 1',
                'facilities'  => ['Proyektor', 'Whiteboard', 'AC', 'TV Monitor', 'WiFi'],
                'status'      => 'active',
            ],
            [
                'name'        => 'Meeting Room B',
                'code'        => 'MRB',
                'capacity'    => 20,
                'description' => 'Ruang meeting besar untuk presentasi dan workshop.',
                'location'    => 'Lantai 2',
                'facilities'  => ['Proyektor', 'Whiteboard', 'AC', 'Sound System', 'WiFi', 'Video Conference'],
                'status'      => 'active',
            ],
            [
                'name'        => 'Board Room',
                'code'        => 'BRD',
                'capacity'    => 8,
                'description' => 'Ruang rapat eksekutif dengan fasilitas lengkap.',
                'location'    => 'Lantai 3',
                'facilities'  => ['Smart TV', 'Video Conference', 'AC', 'WiFi', 'Telepon'],
                'status'      => 'active',
            ],
        ];

        foreach ($rooms as $room) {
            \App\Models\Room::updateOrCreate(['code' => $room['code']], $room);
        }
    }
}
