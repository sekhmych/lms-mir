<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\ExternalRequest;
use Illuminate\View\View;

class MyCourseController extends Controller
{
    public function index(): View
    {
        $enrollments = Enrollment::query()
            ->where('user_id', auth()->id())
            ->with(['session.course.direction', 'session.trainer'])
            ->orderByDesc('created_at')
            ->get();

        $approvedRequests = ExternalRequest::query()
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->orderBy('start_date')
            ->get();

        return view('my-courses.index', compact('enrollments', 'approvedRequests'));
    }

    public function show(Enrollment $enrollment): View
    {
        if ($enrollment->user_id !== auth()->id()) {
            abort(403);
        }

        $enrollment->load(['session.course.lessons', 'session.course.direction', 'session.trainer']);

        return view('my-courses.show', compact('enrollment'));
    }
}
