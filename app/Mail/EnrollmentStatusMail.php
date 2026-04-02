<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    private const STATUS_LABELS = [
        'registered' => 'Записан',
        'in_progress' => 'В процессе',
        'completed' => 'Завершён',
        'failed' => 'Не сдан',
        'cancelled' => 'Отменён',
    ];

    public function __construct(
        public Enrollment $enrollment,
        public ?string $oldStatus,
        public string $newStatus,
    ) {}

    public function envelope(): Envelope
    {
        $courseName = $this->enrollment->session?->course?->title ?? 'Курс';

        if ($this->oldStatus === null) {
            $subject = "Запись на курс: {$courseName}";
        } else {
            $label = self::STATUS_LABELS[$this->newStatus] ?? $this->newStatus;
            $subject = "Изменение статуса: {$courseName} — {$label}";
        }

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-status',
            with: [
                'statusLabel' => self::STATUS_LABELS[$this->newStatus] ?? $this->newStatus,
                'oldStatusLabel' => $this->oldStatus ? (self::STATUS_LABELS[$this->oldStatus] ?? $this->oldStatus) : null,
            ],
        );
    }
}
