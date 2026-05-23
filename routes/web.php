<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

// PUBLIC Routes (no authentication required)
Route::get('/', [BookingController::class, 'showForm'])->name('booking.form');
Route::get('/bookings', fn() => redirect()->route('booking.form'));
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/pending/{code}', [BookingController::class, 'pending'])->name('booking.pending');
Route::get('/bookings/success/{code}', [BookingController::class, 'success'])->name('booking.success');
Route::get('/verify/{token}', [BookingController::class, 'verify'])->name('booking.verify');
Route::get('/cancel/{token}', [BookingController::class, 'cancelForm'])->name('booking.cancel.form');
Route::post('/cancel/{token}', [BookingController::class, 'cancel'])->name('booking.cancel');
Route::get('/api/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check-availability');
Route::get('/api/room-schedule', [BookingController::class, 'roomSchedule'])->name('api.room-schedule');
Route::get('/api/room-monthly', [BookingController::class, 'roomMonthly'])->name('api.room-monthly');

// Admin Authentication Routes
Route::get('/admin/login', [AuthController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ADMIN Routes (with authentication)
Route::middleware(\App\Http\Middleware\AdminAuth::class)->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/calendar-data', [AdminController::class, 'calendarData'])->name('calendar-data');

    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/create', [AdminController::class, 'createBooking'])->name('bookings.create');
    Route::post('/bookings/create', [AdminController::class, 'storeBooking'])->name('bookings.store');
    Route::get('/bookings/{id}', [AdminController::class, 'showBooking'])->name('bookings.show');
    Route::get('/bookings/{id}/cancel', fn($id) => redirect()->route('admin.bookings.show', $id));
    Route::post('/bookings/{id}/cancel', [AdminController::class, 'cancelBooking'])->name('bookings.cancel');

    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{id}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{id}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{id}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
});
