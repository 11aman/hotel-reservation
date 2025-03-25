<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Guest;
use App\Models\Room;
use Livewire\Component;
use Symfony\Component\Console\Event\ConsoleEvent;

class RoomBooking extends Component
{
    public $guestName;
    public $roomCount;
    public $availableRooms = [];

    public function mount()
    {
        $this->refreshAvailableRooms();
    }

    public function refreshAvailableRooms()
    {
        $bookedRoomIds = BookingRoom::pluck('room_id')->toArray();
        $this->availableRooms = Room::whereNotIn('id', $bookedRoomIds)->pluck('room_number')->toArray();
        $this->dispatch('refreshRooms');
    }

    public function bookRooms()
    {
        $this->validate([
            'guestName' => 'required',
            'roomCount' => 'required|integer|min:1',
        ]);

        if ($this->roomCount > 5) {
            $this->dispatch('bookingError', 'You cannot book more than 5 rooms.');
            return;
        }

        $guest = Guest::create(['name' => $this->guestName]);

        $rooms = Room::whereNotIn('id', BookingRoom::pluck('room_id'))->take($this->roomCount)->get();

        if ($rooms->count() < $this->roomCount) {
            $this->dispatch('bookingError', 'Not enough rooms available.');
            return;
        }

        $booking = Booking::create(['guest_id' => $guest->id, 'total_travel_time' => 0]);

        foreach ($rooms as $room) {
            BookingRoom::create(['booking_id' => $booking->id, 'room_id' => $room->id]);
            $room->update(['is_booked' => true]);
        }

        $this->dispatch('bookingSuccess', 'Your room has been booked successfully!');
        $this->refreshAvailableRooms();
        $this->reset(['guestName', 'roomCount']);
    }

    public function resetBookings()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Disable FK checks
        BookingRoom::truncate();
        Booking::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Enable FK checks
    
        Room::query()->update(['is_booked' => false]);
    
        $this->refreshAvailableRooms();
        $this->dispatch('refreshRooms');
        $this->dispatch('bookingSuccess', 'All bookings have been reset.');
    }    

    public function randomBooking()
    {
        $availableRooms = Room::whereNotIn('id', BookingRoom::pluck('room_id'))->pluck('id')->toArray();

        if (empty($availableRooms)) {
            $this->dispatch('bookingError', 'No rooms available!');
            return;
        }

        $roomCount = rand(1, 5);
        $randomRooms = array_rand($availableRooms, min($roomCount, count($availableRooms)));

        $guest = Guest::create(['name' => 'Random Guest']);
        $booking = Booking::create(['guest_id' => $guest->id, 'total_travel_time' => 0]);

        foreach ((array) $randomRooms as $roomKey) {
            BookingRoom::create(['booking_id' => $booking->id, 'room_id' => $availableRooms[$roomKey]]);
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
