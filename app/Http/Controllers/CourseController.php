<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function show(Course $course): View
    {
        $course->load(['direction', 'sessions' => fn($q) => $q->where('status', '!=', 'cancelled')->orderBy('start_date')]);

        return view('courses.show', compact('course'));
    }

    public function index(): View
    {
        $courses = Course::query()
            ->where('type', 'internal')
            ->with(['direction', 'sessions' => fn($q) => $q->where('status', '!=', 'cancelled')->orderBy('start_date')])
            ->orderBy('title')
            ->paginate(6);

        return view('courses.index', compact('courses'));
    }
}
