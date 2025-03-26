#Hotel Room Booking System  

#Overview

This is a Laravel Livewire-based hotel room booking system that allows users to book rooms dynamically, with real-time updates and an interactive UI. The system ensures that rooms are booked efficiently while considering travel time within the building.

#Features

Livewire-powered booking system: Instant updates without page reloads.

Room Availability Management: Displays available and booked rooms.

Smart Booking Algorithm: Prioritizes rooms on the same floor first and minimizes travel time.

Random Room Booking: Auto-books a random number of available rooms.

Reset Bookings: Clears all bookings and makes rooms available again.

Tooltip Display: Shows room details on hover.

Building Structure & Travel Time Calculation

Rooms are sequentially numbered (e.g., 101, 102, ..., 110, 201, ..., 210).

Stairs and lift are on the left side of the building.


#Installation

1. Clone the Repository

    git clone https://github.com/11aman/hotel-reservation.git
    cd hotel-booking

2. Install Dependencies

    composer install
    npm install && npm run dev

3. Configure Environment

Copy the .env.example file and update database credentials:

    cp .env.example .env
    php artisan key:generate

4. Run Migrations & Seed Database

    php artisan migrate --seed

5. Start Development Server

    php artisan serve



#Technologies Used

Laravel 10 (Backend)

Livewire (Dynamic UI without page reloads)

Tailwind CSS (Styling)

Alpine.js (Tooltip & Interactivity)

Author

Developed by Aman Shrivastava

License

This project is open-source and available under the MIT License.

