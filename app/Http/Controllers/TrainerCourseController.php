<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Direction;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerCourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::query()
            ->where('created_by', auth()->id())
            ->where('type', 'internal')
            ->withCount('lessons')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('trainer.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $directions = Direction::query()->orderBy('id')->get();

        return view('trainer.courses.create', compact('directions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'direction_id' => ['nullable', 'exists:directions,id'],
            'duration' => ['nullable', 'integer', 'min:1'],
        ]);

        Course::create([
            ...$data,
            'type' => 'internal',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('trainer-courses.index')
            ->with('success', 'Курс успешно создан.');
    }

    public function show(Course $course): View
    {
        if ($course->created_by !== auth()->id()) {
            abort(403);
        }

        $course->load('lessons', 'direction');

        return view('trainer.courses.show', compact('course'));
    }

    public function storeLesson(Request $request, Course $course): RedirectResponse
    {
        if ($course->created_by !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'link' => ['nullable', 'url', 'max:2048'],
        ]);

        $maxOrder = $course->lessons()->max('sort_order') ?? 0;

        $course->lessons()->create([
            ...$data,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Урок добавлен.');
    }

    public function destroyLesson(Course $course, Lesson $lesson): RedirectResponse
    {
        if ($course->created_by !== auth()->id()) {
            abort(403);
        }

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $lesson->delete();

        return back()->with('success', 'Урок удалён.');
    }
}
