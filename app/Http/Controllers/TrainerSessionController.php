<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainerSessionController extends Controller
{
    public function index(): View
    {
        $sessions = CourseSession::query()
            ->where('trainer_id', auth()->id())
            ->with(['course.direction', 'enrollments.user'])
            ->orderBy('start_date')
            ->paginate(10);

        return view('trainer.sessions.index', compact('sessions'));
    }

    public function show(CourseSession $session): View
    {
        if ($session->trainer_id !== auth()->id()) {
            abort(403);
        }

        $session->load(['course.direction', 'enrollments.user', 'trainer']);
        $enrollments = $session->enrollments;

        $enrolledUserIds = $enrollments->pluck('user_id');
        $availableEmployees = User::query()
            ->whereNotIn('id', $enrolledUserIds)
            ->orderBy('name')
            ->get();

        return view('trainer.sessions.show', compact('session', 'enrollments', 'availableEmployees'));
    }

    public function updateEnrollmentStatus(Enrollment $enrollment, Request $request, EnrollmentService $service): RedirectResponse
    {
        if ($enrollment->session && $enrollment->session->trainer_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'in:registered,in_progress,completed,failed,cancelled'],
        ]);

        $service->updateStatus($enrollment, $data['status']);

        return back()->with('success', 'Статус обновлён.');
    }

    public function uploadCertificate(Enrollment $enrollment, Request $request): RedirectResponse
    {
        // Проверяем, что это поток текущего тренера
        if ($enrollment->session && $enrollment->session->trainer_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $path = $request->file('certificate')->store('certificates', 'public');
        $enrollment->update(['certificate_path' => $path]);

        return back()->with('success', 'Сертификат загружен.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
        ]);

        CourseSession::create([
            ...$data,
            'trainer_id' => auth()->id(),
            'status' => 'planned',
        ]);

        return redirect()->route('trainer-sessions.index')
            ->with('success', 'Поток успешно создан.');
    }

    public function create(): View
    {
        $courses = Course::query()
            ->where('type', 'internal')
            ->orderBy('title')
            ->get();

        return view('trainer.sessions.create', compact('courses'));
    }

    public function enrollEmployee(CourseSession $session, Request $request, EnrollmentService $service): RedirectResponse
    {
        if ($session->trainer_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $exists = $session->enrollments()
            ->where('user_id', $data['user_id'])
            ->exists();

        if ($exists) {
            return back()->with('warning', 'Этот сотрудник уже записан на поток.');
        }

        $service->createEnrollment($session, $data['user_id']);

        return back()->with('success', 'Сотрудник записан на поток.');
    }
}
