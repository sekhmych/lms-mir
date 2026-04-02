<div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2>{{ $oldStatusLabel ? 'Изменение статуса обучения' : 'Запись на курс' }}</h2>

    <p>Здравствуйте, {{ $enrollment->user->name }}.</p>

    @if (!$oldStatusLabel)
        <p>Вы записаны на курс <strong>{{ $enrollment->session?->course?->title }}</strong>.</p>
    @else
        <p>Статус вашего обучения по курсу <strong>{{ $enrollment->session?->course?->title }}</strong> изменён.</p>
        <p>
            <strong>Прежний статус:</strong> {{ $oldStatusLabel }}<br>
            <strong>Новый статус:</strong> {{ $statusLabel }}
        </p>
    @endif

    @if ($enrollment->session)
        <p>
            <strong>Даты:</strong> {{ $enrollment->session->start_date }} — {{ $enrollment->session->end_date }}
            @if ($enrollment->session->location)
                <br><strong>Место:</strong> {{ $enrollment->session->location }}
            @endif
        </p>
    @endif
</div>
