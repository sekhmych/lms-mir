<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Enrollment;
use App\Models\ExternalRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HrController extends Controller
{
    public function index(): View
    {
        $totalEmployees = User::role('employee')->count();
        $totalTrainers = User::role('trainer')->count();
        $totalCourses = Course::query()->count();
        $internalCourses = Course::query()->where('type', 'internal')->count();
        $externalCourses = Course::query()->where('type', 'external')->count();

        $totalSessions = CourseSession::query()->count();
        $activeSessions = CourseSession::query()->whereIn('status', ['planned', 'ongoing'])->count();
        $completedSessions = CourseSession::query()->where('status', 'completed')->count();

        $totalEnrollments = Enrollment::query()->count();
        $completedEnrollments = Enrollment::query()->where('status', 'completed')->count();
        $inProgressEnrollments = Enrollment::query()->where('status', 'in_progress')->count();
        $failedEnrollments = Enrollment::query()->where('status', 'failed')->count();

        $pendingExternalRequests = ExternalRequest::query()->where('status', 'pending')->count();
        $approvedExternalRequests = ExternalRequest::query()->where('status', 'approved')->count();
        $totalExternalRequests = ExternalRequest::query()->count();

        $stats = compact(
            'totalEmployees', 'totalTrainers', 'totalCourses', 'internalCourses', 'externalCourses',
            'totalSessions', 'activeSessions', 'completedSessions',
            'totalEnrollments', 'completedEnrollments', 'inProgressEnrollments', 'failedEnrollments',
            'pendingExternalRequests', 'approvedExternalRequests', 'totalExternalRequests',
        );

        return view('hr.index', compact('stats'));
    }

    public function exportExcel(): StreamedResponse
    {
        $enrollments = Enrollment::query()
            ->with(['user', 'session.course'])
            ->orderBy('user_id')
            ->get();

        $externalRequests = ExternalRequest::query()
            ->with('user')
            ->orderBy('user_id')
            ->get();

        $spreadsheet = new Spreadsheet();

        // Sheet 1: Internal enrollments
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Внутренние обучения');
        $sheet->setCellValue('A1', 'ФИО');
        $sheet->setCellValue('B1', 'Курс');
        $sheet->setCellValue('C1', 'Статус прохождения');

        $statusLabels = [
            'registered' => 'Записан',
            'in_progress' => 'В процессе',
            'completed' => 'Завершён',
            'failed' => 'Не сдан',
            'cancelled' => 'Отменён',
        ];

        $row = 2;
        foreach ($enrollments as $enrollment) {
            $sheet->setCellValue("A{$row}", $enrollment->user?->name ?? '—');
            $sheet->setCellValue("B{$row}", $enrollment->session?->course?->title ?? '—');
            $sheet->setCellValue("C{$row}", $statusLabels[$enrollment->status] ?? $enrollment->status);
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Sheet 2: External requests
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Внешние обучения');
        $sheet2->setCellValue('A1', 'ФИО');
        $sheet2->setCellValue('B1', 'Курс');
        $sheet2->setCellValue('C1', 'Статус');

        $extStatusLabels = [
            'pending' => 'На рассмотрении',
            'approved' => 'Одобрена',
            'rejected' => 'Отклонена',
            'completed' => 'Завершена',
        ];

        $row = 2;
        foreach ($externalRequests as $extReq) {
            $sheet2->setCellValue("A{$row}", $extReq->user?->name ?? '—');
            $sheet2->setCellValue("B{$row}", $extReq->course_title);
            $sheet2->setCellValue("C{$row}", $extStatusLabels[$extReq->status] ?? $extReq->status);
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet2->getStyle('A1:C1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'otchet-obuchenie-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function externalRequests(): View
    {
        $requests = ExternalRequest::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('hr.external-requests', compact('requests'));
    }

    public function updateExternalRequest(ExternalRequest $externalRequest, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $externalRequest->update(['status' => $data['status']]);

        Approval::create([
            'external_request_id' => $externalRequest->id,
            'approver_id' => auth()->id(),
            'status' => $data['status'],
            'comment' => $data['comment'] ?? null,
            'approved_at' => now(),
        ]);

        $label = $data['status'] === 'approved' ? 'одобрена' : 'отклонена';

        return back()->with('success', "Заявка {$label}.");
    }

    public function sessions(): View
    {
        $sessions = CourseSession::query()
            ->with(['course.direction', 'trainer', 'enrollments'])
            ->orderByDesc('start_date')
            ->paginate(15);

        return view('hr.sessions', compact('sessions'));
    }

    public function sessionShow(CourseSession $session): View
    {
        $session->load(['course.direction', 'enrollments.user', 'trainer']);
        $enrollments = $session->enrollments;

        $enrolledUserIds = $enrollments->pluck('user_id');
        $availableUsers = User::query()
            ->whereNotIn('id', $enrolledUserIds)
            ->orderBy('name')
            ->get();

        return view('hr.session-show', compact('session', 'enrollments', 'availableUsers'));
    }

    public function enrollEmployee(CourseSession $session, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $exists = $session->enrollments()
            ->where('user_id', $data['user_id'])
            ->exists();

        if ($exists) {
            return back()->with('warning', 'Этот сотрудник уже записан на поток.');
        }

        $session->enrollments()->create([
            'user_id' => $data['user_id'],
            'course_id' => $session->course_id,
            'course_session_id' => $session->id,
            'status' => 'registered',
            'progress' => 0,
        ]);

        return back()->with('success', 'Сотрудник записан на поток.');
    }
}
