<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ExternalRequestController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\TrainerSessionController;
use App\Http\Controllers\TrainerCourseController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\MicrosoftAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/msgraph/oauth', [MicrosoftAccountController::class, 'connect'])->name('msgraph.connect');
Route::get('/msgraph', fn () => redirect()->route('profile.edit')->with('status', 'microsoft-connected'))->name('msgraph.landing');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/microsoft/disconnect', [MicrosoftAccountController::class, 'disconnect'])->name('profile.microsoft.disconnect');
});

Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/my-courses', [MyCourseController::class, 'index'])->name('my-courses.index');
    Route::get('/my-courses/{enrollment}', [MyCourseController::class, 'show'])->name('my-courses.show');

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
    Route::post('/trainer/sessions/{session}/enroll', [TrainerSessionController::class, 'enrollEmployee'])->name('trainer-sessions.enroll');

    Route::get('/trainer/courses', [TrainerCourseController::class, 'index'])->name('trainer-courses.index');
    Route::get('/trainer/courses/create', [TrainerCourseController::class, 'create'])->name('trainer-courses.create');
    Route::post('/trainer/courses', [TrainerCourseController::class, 'store'])->name('trainer-courses.store');
    Route::get('/trainer/courses/{course}', [TrainerCourseController::class, 'show'])->name('trainer-courses.show');
    Route::post('/trainer/courses/{course}/lessons', [TrainerCourseController::class, 'storeLesson'])->name('trainer-courses.lessons.store');
    Route::delete('/trainer/courses/{course}/lessons/{lesson}', [TrainerCourseController::class, 'destroyLesson'])->name('trainer-courses.lessons.destroy');
});

Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/', [HrController::class, 'index'])->name('index');
    Route::get('/export', [HrController::class, 'exportExcel'])->name('export');
    Route::get('/external-requests', [HrController::class, 'externalRequests'])->name('external-requests');
    Route::patch('/external-requests/{externalRequest}', [HrController::class, 'updateExternalRequest'])->name('external-requests.update');
    Route::get('/sessions', [HrController::class, 'sessions'])->name('sessions');
    Route::get('/sessions/{session}', [HrController::class, 'sessionShow'])->name('sessions.show');
    Route::post('/sessions/{session}/enroll', [HrController::class, 'enrollEmployee'])->name('sessions.enroll');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/stepik/sync', [DashboardController::class, 'syncStepik'])->name('stepik.sync');
    Route::post('/calendar/sync', [DashboardController::class, 'syncCalendar'])->name('calendar.sync');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/calendar', [AdminUserController::class, 'calendar'])->name('users.calendar');
});

require __DIR__.'/auth.php';
