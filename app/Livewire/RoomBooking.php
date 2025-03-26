<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;

class RoomBooking extends Component
{
    public $roomCount;
    public $availableRooms = [];

    public function mount()
    {
        $this->refreshAvailableRooms();
    }

    public function refreshAvailableRooms()
    {
        $this->availableRooms = Room::where('is_booked', false)->pluck('room_number')->toArray();
        $this->dispatch('refreshRooms');
    }

    public function bookRooms()
    {
        $this->validate([
            'roomCount' => 'required|integer|min:1',
        ]);

        if ($this->roomCount > 5) {
            $this->dispatch('bookingError', 'You cannot book more than 5 rooms.');
            return;
        }
        // Group available rooms by floor
        $availableRoomsByFloor = Room::where('is_booked', false)
            ->orderBy('floor_number')
            ->orderBy('room_number')
            ->get()
            ->groupBy('floor_number');

        $selectedRooms = collect();

        // Try to book rooms on the same floor first
        foreach ($availableRoomsByFloor as $floorRooms) {
            if ($floorRooms->count() >= $this->roomCount) {
                $selectedRooms = $floorRooms->take($this->roomCount);
                break;
            }
        }

        // If not enough rooms on one floor, select rooms with minimum travel time
        if ($selectedRooms->count() < $this->roomCount) {
            $remainingRooms = $this->roomCount - $selectedRooms->count();
            $extraRooms = Room::where('is_booked', false)
                ->whereNotIn('id', $selectedRooms->pluck('id'))
                ->take($remainingRooms)
                ->get();

            $selectedRooms = $selectedRooms->merge($extraRooms);
        }

        if ($selectedRooms->count() < $this->roomCount) {
            $this->dispatch('bookingError', 'Not enough rooms available.');
            return;
        }

        // Calculate Travel Time
        $floors = $selectedRooms->pluck('floor_number')->unique()->sort()->values();
        $horizontalTravel = $selectedRooms->max('room_number') - $selectedRooms->min('room_number');
        $verticalTravel = ($floors->last() - $floors->first()) * 2; // 2 minutes per floor
        $totalTravelTime = $horizontalTravel + $verticalTravel;

        // Update booked rooms
        foreach ($selectedRooms as $room) {
            $room->update([
                'is_booked' => true,
                'travel_time' => $totalTravelTime,
            ]);
        }

        $this->dispatch('bookingSuccess', 'Rooms booked successfully! Total Travel Time: ' . $totalTravelTime . ' min');
        $this->refreshAvailableRooms();
        $this->reset(['roomCount']);
    }

    public function resetBookings()
    {
        Room::query()->update(['is_booked' => false, 'travel_time' => null]);

        $this->refreshAvailableRooms();
        $this->dispatch('refreshRooms');
        $this->dispatch('bookingSuccess', 'All bookings have been reset.');
    }

    public function randomBooking()
    {
        $availableRooms = Room::where('is_booked', false)->pluck('id')->toArray();

        if (empty($availableRooms)) {
            $this->dispatch('bookingError', 'No rooms available!');
            return;
        }

        $roomCount = rand(1, 5);
        $randomRooms = array_rand($availableRooms, min($roomCount, count($availableRooms)));

        foreach ((array) $randomRooms as $roomKey) {
            Room::where('id', $availableRooms[$roomKey])->update(['is_booked' => true]);
        }

        $this->refreshAvailableRooms();
        $this->dispatch('bookingSuccess', 'Random booking successful!');
    }

    public function render()
    {
        return view('livewire.room-booking', ['rooms' => $this->availableRooms]);
    }
}
