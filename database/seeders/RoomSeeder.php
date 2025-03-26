<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1,10) as $floor) {
            $roomCount = $floor == 10 ? 7 : 10;
            for ($i = 1; $i <= $roomCount; $i++) {
                Room::create([
                    'room_number' => $floor * 100 + $i,
                    'floor_number' => $floor,
                ]);
            }
        }
    }
}
