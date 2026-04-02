<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Enrollment;
use App\Models\ExternalRequest;
use App\Models\StepikCourse;
use App\Models\User;
use App\Services\StepikSyncService;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $adminStats = null;

        if (auth()->user()?->hasRole('admin')) {
            $adminStats = [
                'disk_free_space' => $this->formatBytes((int) disk_free_space(base_path())),
                'total_courses' => Course::query()->count() + StepikCourse::query()->count(),
                'internal_courses' => Course::query()->count(),
                'stepik_courses' => StepikCourse::query()->count(),
            ];
        }

        $ganttTasks = collect();
        $trainerStats = null;
        $directorStats = null;

        if (auth()->user()?->hasRole('director')) {
            $allEnrollments = Enrollment::query()->get();
            $totalEnrollments = $allEnrollments->count();
            $completedEnrollments = $allEnrollments->where('status', 'completed')->count();
            $failedEnrollments = $allEnrollments->where('status', 'failed')->count();
            $inProgressEnrollments = $allEnrollments->where('status', 'in_progress')->count();
            $cancelledEnrollments = $allEnrollments->where('status', 'cancelled')->count();

            $allSessions = CourseSession::query()->get();
            $totalSessions = $allSessions->count();
            $activeSessions = $allSessions->whereIn('status', ['planned', 'ongoing'])->count();
            $completedSessions = $allSessions->where('status', 'completed')->count();

            $totalCourses = Course::query()->count();
            $totalEmployees = User::role('employee')->count();
            $totalTrainers = User::role('trainer')->count();

            $extRequests = ExternalRequest::query()->get();
            $pendingRequests = $extRequests->where('status', 'pending')->count();
            $approvedRequests = $extRequests->where('status', 'approved')->count();
            $rejectedRequests = $extRequests->where('status', 'rejected')->count();
            $totalExtBudget = $extRequests->where('status', 'approved')->sum('cost');

            $completionRate = $totalEnrollments > 0
                ? round($completedEnrollments / $totalEnrollments * 100)
                : 0;

            $enrollmentRate = $totalEmployees > 0
                ? round(Enrollment::query()->distinct('user_id')->count('user_id') / $totalEmployees * 100)
                : 0;

            // Оценка: от "всё плохо" до "всё хорошо"
            $score = 0;
            $maxScore = 0;

            // 1. Процент завершения (вес 40)
            $maxScore += 40;
            $score += (int) round($completionRate * 0.4);

            // 2. Охват сотрудников (вес 30)
            $maxScore += 30;
            $score += (int) round($enrollmentRate * 0.3);

            // 3. Мало отменённых / проваленных (вес 15)
            $maxScore += 15;
            $failRate = $totalEnrollments > 0
                ? ($failedEnrollments + $cancelledEnrollments) / $totalEnrollments
                : 0;
            $score += (int) round((1 - $failRate) * 15);

            // 4. Активность потоков (вес 15)
            $maxScore += 15;
            $score += $activeSessions > 0 ? 15 : ($completedSessions > 0 ? 8 : 0);

            $overallPercent = $maxScore > 0 ? round($score / $maxScore * 100) : 0;

            if ($overallPercent >= 80) {
                $overallVerdict = ['text' => 'Всё отлично', 'class' => 'text-green-700 bg-green-100', 'icon' => '✓'];
            } elseif ($overallPercent >= 60) {
                $overallVerdict = ['text' => 'Хорошо', 'class' => 'text-blue-700 bg-blue-100', 'icon' => '↑'];
            } elseif ($overallPercent >= 40) {
                $overallVerdict = ['text' => 'Удовлетворительно', 'class' => 'text-yellow-700 bg-yellow-100', 'icon' => '–'];
            } elseif ($overallPercent >= 20) {
                $overallVerdict = ['text' => 'Требует внимания', 'class' => 'text-orange-700 bg-orange-100', 'icon' => '!'];
            } else {
                $overallVerdict = ['text' => 'Критическая ситуация', 'class' => 'text-red-700 bg-red-100', 'icon' => '✕'];
            }

            $directorStats = [
                'total_employees' => $totalEmployees,
                'total_trainers' => $totalTrainers,
                'total_courses' => $totalCourses,
                'total_sessions' => $totalSessions,
                'active_sessions' => $activeSessions,
                'completed_sessions' => $completedSessions,
                'total_enrollments' => $totalEnrollments,
                'completed_enrollments' => $completedEnrollments,
                'in_progress_enrollments' => $inProgressEnrollments,
                'failed_enrollments' => $failedEnrollments,
                'cancelled_enrollments' => $cancelledEnrollments,
                'completion_rate' => $completionRate,
                'enrollment_rate' => $enrollmentRate,
                'pending_requests' => $pendingRequests,
                'approved_requests' => $approvedRequests,
                'rejected_requests' => $rejectedRequests,
                'total_ext_budget' => $totalExtBudget,
                'overall_percent' => $overallPercent,
                'overall_verdict' => $overallVerdict,
            ];
        }

        if (auth()->user()?->hasRole('trainer')) {
            $trainerSessions = CourseSession::query()
                ->where('trainer_id', auth()->id())
                ->with('enrollments')
                ->get();

            $trainerCourses = Course::query()
                ->where('created_by', auth()->id())
                ->where('type', 'internal')
                ->count();

            $totalEnrollments = $trainerSessions->sum(fn ($s) => $s->enrollments->count());
            $completedEnrollments = $trainerSessions->sum(fn ($s) => $s->enrollments->where('status', 'completed')->count());

            $trainerStats = [
                'total_sessions' => $trainerSessions->count(),
                'active_sessions' => $trainerSessions->whereIn('status', ['planned', 'ongoing'])->count(),
                'completed_sessions' => $trainerSessions->where('status', 'completed')->count(),
                'total_courses' => $trainerCourses,
                'total_enrollments' => $totalEnrollments,
                'completed_enrollments' => $completedEnrollments,
            ];
        }

        if (auth()->user()?->hasRole('employee')) {
            $enrollments = Enrollment::query()
                ->where('user_id', auth()->id())
                ->whereHas('session')
                ->with(['session.course', 'session.trainer'])
                ->get();

            foreach ($enrollments as $enrollment) {
                $session = $enrollment->session;
                if (! $session || ! $session->start_date || ! $session->end_date) {
                    continue;
                }
                $ganttTasks->push([
                    'id' => 'enrollment-' . $enrollment->id,
                    'name' => $session->course?->title ?? 'Курс',
                    'start' => $session->start_date,
                    'end' => $session->end_date,
                    'progress' => $enrollment->progress ?? 0,
                    'trainer' => $session->trainer?->name ?? '',
                    'type' => 'internal',
                    'status' => $enrollment->status,
                    'custom_class' => match ($session->status) {
                        'completed' => 'bar-completed',
                        'ongoing' => 'bar-ongoing',
                        default => 'bar-planned',
                    },
                ]);
            }

            $externalRequests = ExternalRequest::query()
                ->where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'approved'])
                ->get();

            foreach ($externalRequests as $extReq) {
                if (! $extReq->start_date || ! $extReq->end_date) {
                    continue;
                }
                $ganttTasks->push([
                    'id' => 'external-' . $extReq->id,
                    'name' => $extReq->course_title,
                    'start' => $extReq->start_date,
                    'end' => $extReq->end_date,
                    'progress' => 0,
                    'trainer' => '',
                    'type' => 'external',
                    'status' => $extReq->status,
                    'custom_class' => $extReq->status === 'approved' ? 'bar-approved' : 'bar-pending',
                ]);
            }
        }

        return view('dashboard', compact('adminStats', 'ganttTasks', 'trainerStats', 'directorStats'));
    }

    public function syncStepik(StepikSyncService $stepikSyncService): RedirectResponse
    {
        $count = $stepikSyncService->sync();

        return redirect()
            ->route('dashboard')
            ->with('success', "Синхронизировано {$count} курсов Stepik.");
    }

    public function syncCalendar(EnrollmentService $enrollmentService): RedirectResponse
    {
        $count = $enrollmentService->syncCalendarEvents();

        return redirect()
            ->route('dashboard')
            ->with('success', "Создано {$count} событий в Outlook.");
    }

    private function formatBytes(int|float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = max($bytes, 0);

        for ($unitIndex = 0; $size >= 1024 && $unitIndex < count($units) - 1; $unitIndex++) {
            $size /= 1024;
        }

        return number_format($size, $unitIndex === 0 ? 0 : 2, ',', ' ').' '.$units[$unitIndex];
    }
}