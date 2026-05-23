# PRODUCT REQUIREMENTS DOCUMENT v3.0
## Meeting Room & Zoom Booking System
### Final Version: Laravel + Blade + Tailwind CSS

**Versi:** 3.0 (FINAL)  
**Tanggal:** Mei 2026  
**Status:** Ready for Development  
**Tech Stack:** Laravel 10+ | Blade | Tailwind CSS | MySQL 8+

---

## EXECUTIVE SUMMARY

Sistem booking ruang meeting yang simple, modern, dan user-friendly. Karyawan dapat langsung booking ruang meeting dan Zoom meeting tanpa login - cukup isi form dan confirm email. Admin memiliki dashboard terpisah dengan authentication.

**Stack:** Laravel (Backend + Frontend dengan Blade)

---

## KEY DIFFERENCES: v2.0 → v3.0

```
Technology Stack:
v2.0: Laravel + (React atau Bootstrap) + Complex frontend
v3.0: Laravel + Blade + Tailwind CSS ✅ SIMPLE!

Frontend:
v2.0: React SPA (separate server, build process)
v3.0: Blade templates (no build, single server) ✅

Complexity:
v2.0: High (2 servers, deployment complex)
v3.0: Low (single server, easy deployment) ✅

Development Time:
v2.0: 5-8 weeks
v3.0: 2-3 weeks ✅

Maintenance:
v2.0: High (coordinate frontend + backend)
v3.0: Low (single Laravel codebase) ✅
```

---

## TECHNOLOGY STACK (FINAL)

### Backend
```
✅ Laravel 13+ (PHP Web Framework)
✅ PHP 8.5+ (Language)
✅ MySQL 8+ (Database)
✅ Laravel Eloquent ORM (Database abstraction)
✅ Laravel Queue (Background jobs)
✅ Laravel Mail (Email service)
✅ Laravel Validation (Form validation)
✅ Laravel Routes (URL routing)
```

### Frontend
```
✅ Blade Templates (Server-side rendering)
   → Built-in Laravel templating engine
   → No build process needed
   → Direct PHP integration
   
✅ Tailwind CSS 3+ (Styling)
   → Utility-first CSS framework
   → Responsive by default
   → Easy to customize
   → JIT compilation
   
✅ Alpine.js (Optional interactivity)
   → Lightweight JavaScript (15KB)
   → For real-time form validation
   → Availability checking
   → No build process
   → Can be skipped if not needed
```

### External APIs
```
✅ Zoom Meeting API v2 (Meeting creation)
✅ Google Calendar API (Optional, future)
✅ Microsoft Graph API (Optional, future)
```

### Infrastructure & DevOps
```
✅ Linux Server (Ubuntu 22.04 LTS)
✅ Apache/Nginx (Web server)
✅ PHP-FPM (PHP processor)
✅ SSL/TLS Certificate (HTTPS)
✅ Email Service (SendGrid/Mailgun/SMTP)
✅ Database: MySQL 8.0+
✅ Caching: Redis (optional, for tokens)
```

### Development Tools
```
✅ Composer (PHP dependency manager)
✅ NPM/Yarn (Node package manager - untuk Tailwind)
✅ Git (Version control)
✅ Postman (API testing)
✅ Laravel Artisan (CLI tool)
```

---

## PROJECT FOLDER STRUCTURE

```
meeting-booking/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── BookingController.php         (Public booking API)
│   │   │   ├── AdminController.php          (Admin dashboard)
│   │   │   ├── ZoomController.php           (Zoom integration)
│   │   │   └── AuthController.php           (Admin login)
│   │   ├── Requests/
│   │   │   └── StoreBookingRequest.php      (Validation)
│   │   └── Middleware/
│   │       └── AdminAuth.php                (Auth middleware)
│   │
│   ├── Models/
│   │   ├── Booking.php
│   │   ├── Room.php
│   │   ├── Admin.php
│   │   ├── ZoomMeeting.php
│   │   └── Notification.php
│   │
│   ├── Services/
│   │   ├── ZoomService.php                  (Zoom API integration)
│   │   ├── EmailService.php                 (Email notifications)
│   │   ├── AvailabilityService.php          (Check room availability)
│   │   └── BookingService.php               (Business logic)
│   │
│   ├── Jobs/
│   │   ├── SendBookingConfirmation.php      (Queue)
│   │   └── SendReminder.php                 (Queue)
│   │
│   └── Mail/
│       ├── BookingConfirmation.php          (Mailable)
│       ├── BookingReminder.php              (Mailable)
│       └── BookingCancelled.php             (Mailable)
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php                (Main layout)
│   │   │   └── admin.blade.php              (Admin layout)
│   │   │
│   │   ├── booking/
│   │   │   ├── form.blade.php               (PUBLIC - booking form)
│   │   │   ├── success.blade.php            (Success page)
│   │   │   └── verify.blade.php             (Email verification)
│   │   │
│   │   ├── admin/
│   │   │   ├── login.blade.php              (Admin login)
│   │   │   ├── dashboard.blade.php          (Admin dashboard)
│   │   │   ├── bookings.blade.php           (All bookings list)
│   │   │   ├── bookings-detail.blade.php    (Booking detail)
│   │   │   ├── rooms.blade.php              (Room management)
│   │   │   ├── reports.blade.php            (Analytics)
│   │   │   └── settings.blade.php           (Configuration)
│   │   │
│   │   └── emails/
│   │       ├── booking-confirmation.blade.php
│   │       ├── booking-reminder.blade.php
│   │       ├── booking-cancelled.blade.php
│   │       └── admin-notification.blade.php
│   │
│   ├── css/
│   │   └── app.css                          (Tailwind directives)
│   │
│   └── js/
│       ├── app.js                           (Entrypoint)
│       └── forms.js                         (Alpine.js components - optional)
│
├── database/
│   ├── migrations/
│   │   ├── *_create_bookings_table.php
│   │   ├── *_create_rooms_table.php
│   │   ├── *_create_admins_table.php
│   │   ├── *_create_zoom_meetings_table.php
│   │   ├── *_create_notifications_table.php
│   │   └── *_create_audit_logs_table.php
│   │
│   ├── seeders/
│   │   ├── RoomSeeder.php                   (Initial rooms)
│   │   └── AdminSeeder.php                  (Initial admin)
│   │
│   └── factories/
│       └── BookingFactory.php               (For testing)
│
├── routes/
│   ├── web.php                              (Web routes)
│   ├── api.php                              (API routes)
│   └── admin.php                            (Admin routes - optional)
│
├── config/
│   ├── zoom.php                             (Zoom API config)
│   ├── booking.php                          (Booking rules)
│   └── email.php                            (Email config)
│
├── public/
│   ├── css/
│   │   └── app.css                          (Compiled Tailwind)
│   ├── js/
│   │   └── app.js                           (Compiled JS)
│   └── images/
│       └── logo.svg
│
├── tests/
│   ├── Feature/
│   │   ├── BookingTest.php
│   │   └── AdminTest.php
│   └── Unit/
│       ├── ZoomServiceTest.php
│       └── AvailabilityServiceTest.php
│
├── .env                                     (Environment variables - NOT in git)
├── .env.example                             (Template for .env)
├── composer.json
├── package.json                             (Tailwind build)
├── tailwind.config.js                       (Tailwind configuration)
├── webpack.mix.js                           (Build configuration - optional)
└── README.md
```

---

## BLADE TEMPLATE EXAMPLES

### 1. PUBLIC BOOKING FORM (resources/views/booking/form.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Book Meeting Room')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8">
    
    <h1 class="text-3xl font-bold text-gray-900 mb-2">
      Book a Meeting Room
    </h1>
    <p class="text-gray-600 mb-6">
      Book one of our 3 meeting rooms. Confirmation will be sent to your email.
    </p>
    
    <!-- Booking Form -->
    <form action="{{ route('bookings.store') }}" method="POST" class="space-y-6">
      @csrf
      
      <!-- Row 1: Nama & Email -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
            Full Name *
          </label>
          <input 
            type="text" 
            id="name" 
            name="name" 
            required
            value="{{ old('name') }}"
            placeholder="Your name"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                   focus:ring-2 focus:ring-blue-500 focus:border-transparent
                   @error('name') border-red-500 @enderror">
          @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>
        
        <div>
          <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
            Email *
          </label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            required
            value="{{ old('email') }}"
            placeholder="your.email@company.com"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                   focus:ring-2 focus:ring-blue-500 focus:border-transparent
                   @error('email') border-red-500 @enderror">
          @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>
      
      <!-- Row 2: Phone (optional) -->
      <div>
        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
          Phone / Extension (Optional)
        </label>
        <input 
          type="tel" 
          id="phone" 
          name="phone" 
          value="{{ old('phone') }}"
          placeholder="+62xxx or ext. 123"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      </div>
      
      <!-- Row 3: Room Selection -->
      <div>
        <label for="room_id" class="block text-sm font-semibold text-gray-700 mb-2">
          Select Meeting Room *
        </label>
        <select 
          id="room_id" 
          name="room_id" 
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                 focus:ring-2 focus:ring-blue-500 focus:border-transparent
                 @error('room_id') border-red-500 @enderror">
          <option value="">-- Choose a room --</option>
          @foreach($rooms as $room)
            <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
              {{ $room->name }} (Capacity: {{ $room->capacity }})
            </option>
          @endforeach
        </select>
        @error('room_id')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
      
      <!-- Row 4: Date & Time -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">
            Date *
          </label>
          <input 
            type="date" 
            id="date" 
            name="date" 
            required
            value="{{ old('date') }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                   focus:ring-2 focus:ring-blue-500 focus:border-transparent
                   @error('date') border-red-500 @enderror">
          @error('date')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>
        
        <div>
          <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">
            Start Time *
          </label>
          <input 
            type="time" 
            id="start_time" 
            name="start_time" 
            required
            value="{{ old('start_time', '09:00') }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                   focus:ring-2 focus:ring-blue-500 focus:border-transparent
                   @error('start_time') border-red-500 @enderror">
          @error('start_time')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>
        
        <div>
          <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">
            End Time *
          </label>
          <input 
            type="time" 
            id="end_time" 
            name="end_time" 
            required
            value="{{ old('end_time', '10:00') }}"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                   focus:ring-2 focus:ring-blue-500 focus:border-transparent
                   @error('end_time') border-red-500 @enderror">
          @error('end_time')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>
      
      <!-- Row 5: Topic & Description -->
      <div>
        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
          Meeting Topic *
        </label>
        <input 
          type="text" 
          id="title" 
          name="title" 
          required
          value="{{ old('title') }}"
          placeholder="e.g., Project Kickoff, Team Standup"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                 focus:ring-2 focus:ring-blue-500 focus:border-transparent
                 @error('title') border-red-500 @enderror">
        @error('title')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
      
      <div>
        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
          Description (Optional)
        </label>
        <textarea 
          id="description" 
          name="description" 
          rows="3"
          placeholder="Any additional details..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg 
                 focus:ring-2 focus:ring-blue-500 focus:border-transparent
                 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
      </div>
      
      <!-- Row 6: Zoom Checkbox -->
      <div class="flex items-center space-x-3 bg-blue-50 p-4 rounded-lg">
        <input 
          type="checkbox" 
          id="enable_zoom" 
          name="enable_zoom" 
          value="1"
          @checked(old('enable_zoom'))
          class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
        <label for="enable_zoom" class="text-sm font-medium text-gray-900">
          ☑ Set up Zoom meeting link automatically
        </label>
      </div>
      
      <!-- Submit Button -->
      <div class="pt-4">
        <button 
          type="submit" 
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold 
                 py-3 px-4 rounded-lg transition-colors duration-200">
          Book Meeting Room
        </button>
      </div>
    </form>
    
    <!-- Help Text -->
    <div class="mt-8 pt-8 border-t border-gray-200">
      <h3 class="text-sm font-semibold text-gray-900 mb-2">❓ Need help?</h3>
      <p class="text-sm text-gray-600">
        Confirmation will be sent to your email. Click the link in the email to confirm your booking. 
        You'll receive reminders 24 hours and 1 hour before your meeting.
      </p>
    </div>
  </div>
</div>
@endsection
```

### 2. ADMIN DASHBOARD (resources/views/admin/dashboard.blade.php)

```blade
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="p-8">
  <!-- Header -->
  <div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
    <p class="text-gray-600">Manage meeting rooms and bookings</p>
  </div>
  
  <!-- Stats Cards -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
      <div class="text-gray-600 text-sm font-semibold">Today's Bookings</div>
      <div class="text-3xl font-bold text-gray-900 mt-2">{{ $todayBookings }}</div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
      <div class="text-gray-600 text-sm font-semibold">Pending Confirmations</div>
      <div class="text-3xl font-bold text-blue-600 mt-2">{{ $pendingCount }}</div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
      <div class="text-gray-600 text-sm font-semibold">Rooms Available</div>
      <div class="text-3xl font-bold text-green-600 mt-2">{{ $availableRooms }}/3</div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
      <div class="text-gray-600 text-sm font-semibold">This Month</div>
      <div class="text-3xl font-bold text-purple-600 mt-2">{{ $monthBookings }}</div>
    </div>
  </div>
  
  <!-- Tables -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Bookings -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Recent Bookings</h2>
      </div>
      
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Room</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Time</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
            <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($recentBookings as $booking)
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm text-gray-900">{{ $booking->name }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->room->name }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">
                {{ $booking->start_time->format('M d, H:i') }}
              </td>
              <td class="px-6 py-4 text-sm">
                @if($booking->status === 'confirmed')
                  <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                    Confirmed
                  </span>
                @elseif($booking->status === 'pending')
                  <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                    Pending
                  </span>
                @else
                  <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                    Cancelled
                  </span>
                @endif
              </td>
              <td class="px-6 py-4 text-right">
                <a href="{{ route('admin.bookings.show', $booking) }}" 
                   class="text-blue-600 hover:text-blue-900 text-sm font-semibold">
                  View
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                No bookings yet
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <!-- Room Status -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Room Status</h2>
      </div>
      
      <div class="divide-y divide-gray-200">
        @foreach($rooms as $room)
          <div class="px-6 py-4">
            <div class="flex justify-between items-center mb-2">
              <h3 class="font-semibold text-gray-900">{{ $room->name }}</h3>
              <span class="@if($room->status === 'active') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif 
                           px-2 py-1 rounded text-xs font-semibold">
                {{ ucfirst($room->status) }}
              </span>
            </div>
            <p class="text-sm text-gray-600">Capacity: {{ $room->capacity }} people</p>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
```

---

## TAILWIND CSS CONFIGURATION

### tailwind.config.js
```javascript
module.exports = {
  content: [
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          500: '#0284c7',
          600: '#0369a1',
          700: '#075985',
        }
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
```

### resources/css/app.css
```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom components */
@layer components {
  .btn-primary {
    @apply px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors;
  }
  
  .btn-secondary {
    @apply px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition-colors;
  }
  
  .form-input {
    @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent;
  }
}
```

---

## PACKAGE.JSON BUILD SETUP

```json
{
  "private": true,
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview",
    "tailwind": "tailwindcss -i ./resources/css/app.css -o ./public/css/app.css --watch"
  },
  "devDependencies": {
    "@tailwindcss/forms": "^0.5.3",
    "@tailwindcss/typography": "^0.5.9",
    "alpinejs": "^3.12.0",
    "autoprefixer": "^10.4.14",
    "postcss": "^8.4.24",
    "tailwindcss": "^3.3.0",
    "vite": "^4.3.9"
  }
}
```

**Commands:**
```bash
# Install dependencies
npm install

# Development build (watch mode)
npm run dev

# Production build
npm run build

# Build Tailwind CSS only
npm run tailwind
```

---

## LARAVEL CONFIGURATION FILES

### config/zoom.php
```php
<?php

return [
    'client_id' => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'account_id' => env('ZOOM_ACCOUNT_ID'),
    'base_url' => env('ZOOM_API_BASE_URL', 'https://api.zoom.us/v2'),
    'cache_ttl' => 3600, // Token cache 1 hour
];
```

### config/booking.php
```php
<?php

return [
    'email_domain' => env('BOOKING_EMAIL_DOMAIN', '@company.com'),
    'rate_limit' => [
        'per_ip_per_hour' => 10,
        'enabled' => true,
    ],
    'verification_token_expiry' => 24, // hours
    'booking_rules' => [
        'min_advance_hours' => 0, // Can book immediately
        'max_advance_days' => 90,
    ],
];
```

### .env.example
```env
# Application
APP_NAME="Meeting Room Booking"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://booking.company.com
APP_KEY=

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meeting_booking
DB_USERNAME=root
DB_PASSWORD=

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_FROM_ADDRESS=booking@company.com
MAIL_FROM_NAME="Meeting Room Booking"
MAIL_USERNAME=
MAIL_PASSWORD=

# Zoom API
ZOOM_CLIENT_ID=
ZOOM_CLIENT_SECRET=
ZOOM_ACCOUNT_ID=

# Booking Config
BOOKING_EMAIL_DOMAIN=@company.com
```

---

## ROUTES CONFIGURATION

### routes/web.php
```php
<?php

use Illuminate\Support\Facades\Route;

// PUBLIC Routes (no authentication)
Route::prefix('/')->group(function () {
    Route::get('/', 'BookingController@showForm')->name('booking.form');
    Route::post('/bookings', 'BookingController@store')->name('bookings.store');
    Route::get('/bookings/success/{token}', 'BookingController@success')->name('booking.success');
    Route::get('/verify/{token}', 'BookingController@verify')->name('booking.verify');
    Route::get('/cancel/{token}', 'BookingController@cancel')->name('booking.cancel');
    Route::get('/reschedule/{token}', 'BookingController@rescheduleForm')->name('booking.reschedule');
});

// ADMIN Routes (with authentication)
Route::middleware('auth:admin')->prefix('/admin')->group(function () {
    Route::get('/dashboard', 'AdminController@dashboard')->name('admin.dashboard');
    Route::get('/bookings', 'AdminController@bookings')->name('admin.bookings');
    Route::get('/bookings/{id}', 'AdminController@showBooking')->name('admin.bookings.show');
    Route::post('/bookings/{id}/cancel', 'AdminController@cancelBooking')->name('admin.bookings.cancel');
    
    Route::get('/rooms', 'RoomController@index')->name('admin.rooms');
    Route::get('/rooms/create', 'RoomController@create')->name('admin.rooms.create');
    Route::post('/rooms', 'RoomController@store')->name('admin.rooms.store');
    Route::get('/rooms/{id}/edit', 'RoomController@edit')->name('admin.rooms.edit');
    Route::put('/rooms/{id}', 'RoomController@update')->name('admin.rooms.update');
    
    Route::get('/reports', 'ReportController@index')->name('admin.reports');
    Route::get('/logout', 'AuthController@logout')->name('admin.logout');
});

// Admin Authentication Routes
Route::get('/admin/login', 'AuthController@loginForm')->name('admin.login');
Route::post('/admin/login', 'AuthController@login');
```

### routes/api.php
```php
<?php

use Illuminate\Support\Facades\Route;

// PUBLIC API (no authentication)
Route::post('/bookings', 'BookingController@store');
Route::get('/bookings/check-availability', 'BookingController@checkAvailability');
Route::post('/bookings/{token}/verify', 'BookingController@verify');
Route::delete('/bookings/{token}/cancel', 'BookingController@cancel');

// ADMIN API (with authentication)
Route::middleware('auth:api')->group(function () {
    Route::get('/admin/bookings', 'AdminController@bookingsApi');
    Route::get('/admin/stats', 'AdminController@stats');
});
```

---

## IMPLEMENTATION ROADMAP (WITH BLADE)

### Phase 1: Week 1-2 (Core Booking)
```
✅ Setup Laravel project
✅ Create database migrations
✅ Build public booking form (Blade template)
✅ Implement form validation
✅ Email verification flow
✅ Booking API endpoints
✅ Notification emails
```

### Phase 2: Week 3 (Admin Area)
```
✅ Admin authentication
✅ Admin dashboard (Blade templates)
✅ Booking management UI
✅ Room management UI
✅ Basic reports
```

### Phase 3: Week 4 (Zoom Integration)
```
✅ Zoom API setup (already planned in v2.0)
✅ Auto-generate meeting links
✅ Add Zoom link to emails
```

### Phase 4: Week 5+ (Polish)
```
✅ Mobile responsiveness (Tailwind built-in)
✅ Error handling
✅ UI/UX refinements
✅ Performance optimization
✅ Testing
```

---

## BUILD & DEPLOYMENT

### Setup Tailwind CSS
```bash
# Install dependencies
npm install

# Watch for changes (development)
npm run tailwind

# OR build once (production)
npm run build
```

### Deploy to Server
```bash
# 1. Clone repository
git clone <repo> meeting-booking
cd meeting-booking

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Build assets
npm run build

# 5. Setup database
php artisan migrate
php artisan db:seed

# 6. Set permissions
chmod -R 775 storage bootstrap/cache

# 7. Start server
php artisan serve
# OR with Apache/Nginx - configure vhost
```

---

## KEY ADVANTAGES: BLADE + TAILWIND

| Aspek | Benefit |
|-------|---------|
| **Setup** | Zero setup - comes with Laravel |
| **Build** | Optional - Tailwind JIT compilation |
| **Deployment** | Single server, no complex pipeline |
| **Performance** | Server-side rendered = fast initial load |
| **Maintenance** | Single codebase = easier to maintain |
| **Learning** | Laravel developers already know Blade |
| **Scalability** | Laravel handles routing & caching |
| **Modern UI** | Tailwind provides modern aesthetic |
| **Responsive** | Tailwind responsive utilities out of box |
| **Cost** | Lower hosting costs (single server) |

---

## SUMMARY: WHAT YOU GET

```
✅ Simple, modern booking form (no login required)
✅ Admin dashboard (with login)
✅ Responsive design (mobile-friendly via Tailwind)
✅ Zoom meeting integration
✅ Email notifications
✅ Real-time availability checking
✅ Easy deployment (single Laravel app)
✅ Easy maintenance (one codebase)
✅ Production-ready security
✅ Fast development time (2-3 weeks)
```

---

**Document Version:** 3.0 (FINAL)  
**Tech Stack:** Laravel 13+ | Blade | Tailwind CSS | MySQL  
**Status:** Ready for Development  
**Last Updated:** Mei 2026
