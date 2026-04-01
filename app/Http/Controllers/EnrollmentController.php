<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(CourseSession $session): RedirectResponse
    {
        $user = auth()->user();

        $existing = $session->enrollments()
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('warning', 'Вы уже записаны на этот поток.');
        }

        
        $session->enrollments()->create([
            'user_id'          => $user->id,
            'course_id'        => $session->course_id,
            'course_session_id' => $session->id,
            'status'           => 'registered',
            'progress'         => 0,
        ]);

        return back()->with('success', 'Вы успешно записаны на поток.');
    }
}
