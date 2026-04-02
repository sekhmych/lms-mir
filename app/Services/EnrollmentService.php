<?php

namespace App\Services;

use App\Mail\EnrollmentStatusMail;
use App\Models\Enrollment;
use App\Models\CourseSession;
use App\Models\User;
use Dcblogdev\MsGraph\MsGraph;
use Dcblogdev\MsGraph\Models\MsGraphToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnrollmentService
{
    private const STATUS_LABELS = [
        'registered' => 'Записан',
        'in_progress' => 'В процессе',
        'completed' => 'Завершён',
        'failed' => 'Не сдан',
        'cancelled' => 'Отменён',
    ];

    public function createEnrollment(CourseSession $session, int $userId): Enrollment
    {
        $enrollment = $session->enrollments()->create([
            'user_id' => $userId,
            'course_id' => $session->course_id,
            'course_session_id' => $session->id,
            'status' => 'registered',
            'progress' => 0,
        ]);

        $enrollment->load(['user', 'session.course']);

        $this->createCalendarEvent($enrollment);
        $this->sendStatusEmail($enrollment, null, 'registered');

        return $enrollment;
    }

    public function updateStatus(Enrollment $enrollment, string $newStatus): void
    {
        $oldStatus = $enrollment->status;
        $enrollment->update(['status' => $newStatus]);
        $enrollment->load(['user', 'session.course']);

        if ($newStatus === 'cancelled') {
            $this->deleteCalendarEvent($enrollment);
        } else {
            $this->updateCalendarEvent($enrollment);
        }

        $this->sendStatusEmail($enrollment, $oldStatus, $newStatus);
    }

    private function createCalendarEvent(Enrollment $enrollment): void
    {
        $user = $enrollment->user;
        $session = $enrollment->session;

        if (!$session || !$this->hasGraphToken($user)) {
            return;
        }

        try {
            $session->load('course');
            $msgraph = new MsGraph();
            MsGraph::login($user);

            $startDateTime = $session->start_date;
            $endDateTime = $session->end_date;

            if ($session->start_time) {
                $startDateTime .= 'T' . $session->start_time;
            } else {
                $startDateTime .= 'T09:00:00';
            }

            if ($session->end_time) {
                $endDateTime .= 'T' . $session->end_time;
            } else {
                $endDateTime .= 'T18:00:00';
            }

            $eventData = [
                'subject' => 'Курс: ' . $session->course->title,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => '<p>Вы записаны на курс <b>' . e($session->course->title) . '</b>.</p>'
                        . ($session->location ? '<p>Место: ' . e($session->location) . '</p>' : ''),
                ],
                'start' => [
                    'dateTime' => $startDateTime,
                    'timeZone' => 'Asia/Almaty',
                ],
                'end' => [
                    'dateTime' => $endDateTime,
                    'timeZone' => 'Asia/Almaty',
                ],
                'isReminderOn' => true,
                'reminderMinutesBeforeStart' => 1440,
            ];

            if ($session->location) {
                $eventData['location'] = ['displayName' => $session->location];
            }

            $response = $msgraph->post('me/events', $eventData, [], $user->id);

            if (isset($response['id'])) {
                $enrollment->update(['calendar_event_id' => $response['id']]);
            }
        } catch (\Exception $e) {
            Log::warning('Calendar event creation failed for enrollment #' . $enrollment->id, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function updateCalendarEvent(Enrollment $enrollment): void
    {
        if (!$enrollment->calendar_event_id || !$this->hasGraphToken($enrollment->user)) {
            return;
        }

        try {
            $msgraph = new MsGraph();
            MsGraph::login($enrollment->user);
            $statusLabel = self::STATUS_LABELS[$enrollment->status] ?? $enrollment->status;

            $msgraph->patch('me/events/' . $enrollment->calendar_event_id, [
                'subject' => 'Курс: ' . $enrollment->session->course->title . ' (' . $statusLabel . ')',
            ], [], $enrollment->user->id);
        } catch (\Exception $e) {
            Log::warning('Calendar event update failed for enrollment #' . $enrollment->id, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function deleteCalendarEvent(Enrollment $enrollment): void
    {
        if (!$enrollment->calendar_event_id || !$this->hasGraphToken($enrollment->user)) {
            return;
        }

        try {
            $msgraph = new MsGraph();
            MsGraph::login($enrollment->user);
            $msgraph->delete('me/events/' . $enrollment->calendar_event_id, [], [], $enrollment->user->id);
            $enrollment->update(['calendar_event_id' => null]);
        } catch (\Exception $e) {
            Log::warning('Calendar event deletion failed for enrollment #' . $enrollment->id, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendStatusEmail(Enrollment $enrollment, ?string $oldStatus, string $newStatus): void
    {
        $user = $enrollment->user;

        if (!$user->email) {
            return;
        }

        try {
            Mail::to($user->email)->send(new EnrollmentStatusMail(
                $enrollment,
                $oldStatus,
                $newStatus,
            ));
        } catch (\Exception $e) {
            Log::warning('Enrollment status email failed for enrollment #' . $enrollment->id, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function hasGraphToken(User $user): bool
    {
        return MsGraphToken::where('user_id', $user->id)->exists();
    }

    public function syncCalendarEvents(): int
    {
        $enrollments = Enrollment::query()
            ->whereNull('calendar_event_id')
            ->whereIn('status', ['registered', 'in_progress'])
            ->whereHas('session')
            ->with(['user', 'session.course'])
            ->get();

        $created = 0;

        foreach ($enrollments as $enrollment) {
            if (!$this->hasGraphToken($enrollment->user)) {
                continue;
            }

            $this->createCalendarEvent($enrollment);
            $enrollment->refresh();

            if ($enrollment->calendar_event_id) {
                $created++;
            }
        }

        return $created;
    }
}
