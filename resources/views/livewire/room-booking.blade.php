<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-r from-blue-400 to-purple-500 p-6">
    <!-- Booking Controls -->
    <div class="flex space-x-2 mb-6">
        <input type="number" placeholder="Rooms (1-5)" min="1" max="5" wire:model="roomCount"
            class="w-24 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-200">
        <button wire:click="bookRooms"
            class="px-5 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-lg shadow-md hover:scale-105 transform transition-all duration-300">
            Book Now
        </button>
        <button wire:click="resetBookings"
            class="px-5 py-3 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:scale-105 transform transition-all duration-300">
            Reset
        </button>
        <button wire:click="randomBooking"
            class="px-5 py-3 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:scale-105 transform transition-all duration-300">
            Random
        </button>
    </div>


    <!-- Success Modal (Alpine.js) -->
    <div x-data="{ show: false, message: '' }" @booking-success.window="message = $event.detail; show = true; setTimeout(() => show = false, 3000)" x-cloak>
        <template x-if="show">
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                    <h3 class="text-xl font-semibold text-green-600">Success!</h3>
                    <p class="text-gray-600 mt-2" x-text="message"></p>
                    <div class="mt-4">
                        <button @click="show = false"
                            class="px-5 py-2 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:scale-105 transform transition-all duration-300">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Error Modal (Alpine.js) -->
    <div x-data="{ error: false, message: '' }" @booking-error.window="message = $event.detail; error = true; setTimeout(() => error = false, 3000)" x-cloak>
        <template x-if="error">
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                    <h3 class="text-xl font-semibold text-red-600">Error!</h3>
                    <p class="text-gray-600 mt-2" x-text="message"></p>
                    <div class="mt-4">
                        <button @click="error = false"
                            class="px-5 py-2 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:scale-105 transform transition-all duration-300">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Room Grid Layout -->
    <div class="flex bg-white p-6 rounded-2xl shadow-xl w-full max-w-5xl">
        <!-- Left Panel: Floor Numbers -->
        <div class="flex flex-col justify-center space-y-4 pr-4 mt-4">
            @foreach(array_reverse(range(1, 10)) as $floor)
                <div class="border w-20 h-20 flex items-center justify-center font-bold text-gray-700">
                    Floor {{ $floor }}
                </div>
            @endforeach
        </div>

        <!-- Room Grid -->
        <div class="grid grid-cols-10 gap-3 mt-4 relative overflow-visible">
            @foreach(array_reverse(range(1, 10)) as $floor)
                @foreach(range(($floor * 100) + 1, ($floor == 10 ? ($floor * 100) + 7 : ($floor * 100) + 10)) as $room)
                <div x-data="{ showTooltip: false }" 
                    @mouseenter="showTooltip = true" 
                    @mouseleave="showTooltip = false"
                    class="relative w-20 h-20 border p-4 flex items-center font-medium rounded-xl shadow-lg justify-center 
                        hover:scale-105 transform transition duration-300 cursor-pointer
                        {{ in_array($room, $rooms) ? 'bg-gray-200 hover:bg-gray-300 text-black' : 'bg-gray-400 text-black' }}">
                    
                    <!-- Room Number -->
                    {{ $room }}

                    <!-- Hover Tooltip -->
                    <div x-show="showTooltip" x-cloak 
                        class="absolute top-[-3.5rem] left-1/2 -translate-x-1/2 bg-white text-gray-700 p-2 
                            rounded-lg shadow-lg text-sm z-[9999] flex flex-col items-center pointer-events-none
                            after:content-[''] after:absolute after:top-full after:left-1/2 
                            after:-translate-x-1/2 after:border-4 after:border-transparent after:border-t-white"
                        style="width: 120px; height: 80px;">
                        <p><strong>Room {{ $room }}</strong></p>
                        <p>Floor: {{ floor($room / 100) }}</p>
                        <p>{{ in_array($room, $rooms) ? 'Available' : 'Booked' }}</p>
                    </div>
                </div>
                @endforeach

                @if($floor == 10)
                    {{-- Add blank spaces after the 7 rooms --}}
                    @for($i = 0; $i < 3; $i++)
                        <div class="w-20 h-20"></div>
                    @endfor
                @endif
            @endforeach
        </div>

    </div>
</div>

<!-- Alpine.js Script to Listen for Livewire Events -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Livewire.on('bookingSuccess', (message) => {
            window.dispatchEvent(new CustomEvent('booking-success', { detail: message }));
        });

        Livewire.on('bookingError', (message) => {
            window.dispatchEvent(new CustomEvent('booking-error', { detail: message }));
        });
    });
</script>
