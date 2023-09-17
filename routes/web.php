<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::middleware('auth')->group(function () {

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/tags/index', [TagController::class, 'index'])->name('tags.index');
    Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create');
    Route::post('/tags/store', [TagController::class, 'store'])->name('tags.store');
    Route::get('/tags/edit/{id}', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('/tags/update/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/delete/{id}', [TagController::class, 'destroy'])->name('tags.destroy');
    Route::post('/tags/subscribe/{id}', [TagController::class, 'subscribe'])->name('tags.subscribe');
    Route::post('/tags/unsubscribe/{id}', [TagController::class, 'unsubscribe'])->name('tags.unsubscribe');
});

Route::middleware('auth')->group(function () {
    Route::get('/events/publish', [NotificationController::class, 'publish'])->name('events.publish');
});

Route::middleware('auth')->group(function () {
    Route::get('/notifications/markread/{id}', [NotificationController::class, 'markread'])->name('notifications.markread');
    Route::get('/notifications/delete/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');
    Route::get('/notifications/getMyNotifications', [NotificationController::class, 'getMyNotifications'])->name('notifications.getMyNotifications');
    Route::get('/notifications/history', [NotificationController::class, 'history'])->name('notifications.history');
});

require __DIR__.'/auth.php';
