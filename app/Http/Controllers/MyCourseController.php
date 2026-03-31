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
}
