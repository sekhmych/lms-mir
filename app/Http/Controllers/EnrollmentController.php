<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;

class EnrollmentController extends Controller
{
    public function store(CourseSession $session, EnrollmentService $service): RedirectResponse
    {
        $user = auth()->user();

        $existing = $session->enrollments()
            ->where('user_id', $user->id)
            ->exists();

        if ($existing) {
            return back()->with('warning', 'Вы уже записаны на этот поток.');
        }

        $service->createEnrollment($session, $user->id);

        return back()->with('success', 'Вы успешно записаны на поток.');
    }
}
