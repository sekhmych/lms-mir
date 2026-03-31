<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExternalRequestController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\TrainerSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/my-courses', [MyCourseController::class, 'index'])->name('my-courses.index');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/sessions/{session}/enroll', [EnrollmentController::class, 'store'])->name('enrollments.store');

    Route::get('/external-requests', [ExternalRequestController::class, 'index'])->name('external-requests.index');
    Route::get('/external-requests/create', [ExternalRequestController::class, 'create'])->name('external-requests.create');
    Route::post('/external-requests', [ExternalRequestController::class, 'store'])->name('external-requests.store');
});

Route::middleware(['auth', 'role:trainer'])->group(function () {
    Route::get('/trainer/sessions', [TrainerSessionController::class, 'index'])->name('trainer-sessions.index');
    Route::get('/trainer/sessions/create', [TrainerSessionController::class, 'create'])->name('trainer-sessions.create');
    Route::post('/trainer/sessions', [TrainerSessionController::class, 'store'])->name('trainer-sessions.store');
    Route::get('/trainer/sessions/{session}', [TrainerSessionController::class, 'show'])->name('trainer-sessions.show');
    Route::patch('/trainer/enrollments/{enrollment}/status', [TrainerSessionController::class, 'updateEnrollmentStatus'])->name('trainer-enrollments.update-status');
    Route::post('/trainer/enrollments/{enrollment}/certificate', [TrainerSessionController::class, 'uploadCertificate'])->name('trainer-enrollments.upload-certificate');
});

require __DIR__.'/auth.php';
