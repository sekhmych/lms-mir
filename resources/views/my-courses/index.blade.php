<x-app-layout>
    <x-slot:header>Мои курсы</x-slot:header>

    <div class="py-12 px-12">
        <div class="">
            <div class="bg-white">
                <div class="p-6 text-gray-900 space-y-10">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Внутренние курсы</h2>

                        @php


                            $enrollmentStatusLabels = [
                                'registered'  => ['label' => 'Записан', 'class' => 'bg-gray-100 text-gray-600'],
                                'in_progress' => ['label' => 'В процессе', 'class' => 'bg-yellow-100 text-yellow-800'],
                                'completed'   => ['label' => 'Завершён', 'class' => 'bg-green-100 text-green-800'],
                                'failed'      => ['label' => 'Не сдан', 'class' => 'bg-red-100 text-red-800'],
                                'cancelled'   => ['label' => 'Отменён', 'class' => 'bg-gray-100 text-gray-500'],
                            ];
                            $sessionStatusLabels = [
                                'planned'   => ['label' => 'Запланирован', 'class' => 'bg-blue-100 text-blue-700'],
                                'ongoing'   => ['label' => 'Идёт', 'class' => 'bg-yellow-100 text-yellow-800'],
                                'completed' => ['label' => 'Завершён', 'class' => 'bg-green-100 text-green-800'],
                                'cancelled' => ['label' => 'Отменён', 'class' => 'bg-gray-100 text-gray-500'],
                            ];


                        @endphp

                        @if ($enrollments->isEmpty())
                            <p class="text-sm text-gray-500">Вы ещё не записаны ни на один поток.</p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($enrollments as $enrollment)
                                    @php
                                        $session  = $enrollment->session;
                                        $course  = $session?->course;
                                        $enrollmentStatus  = $enrollmentStatusLabels[$enrollment->status] ?? ['label' => $enrollment->status, 'class' => 'bg-gray-100 text-gray-600'];
                                        $sessionStatus  = $sessionStatusLabels[$session?->status] ?? ['label' => $session?->status, 'class' => 'bg-gray-100 text-gray-600'];
                                    @endphp
                                    <a href="{{ route('my-courses.show', $enrollment) }}" class="flex flex-col bg-white px-6 py-4 rounded-sm hover:bg-gray-100 transition-colors">
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between gap-2 mb-2">
                                                <h3 class="font-semibold text-gray-900 text-base leading-snug">
                                                    {{ $course?->title ?? 'Курс удалён' }}
                                                </h3>
                                                <span
                                                    class="shrink-0 text-xs font-medium px-2 py-0.5 rounded-full {{ $enrollmentStatus['class'] }}">
                                                    {{ $enrollmentStatus['label'] }}
                                                </span>
                                            </div>

                                            @if ($course?->direction)
                                                <p class="text-xs text-gray-500 mb-3">{{ $course->direction->name }}</p>
                                            @endif

                                            @if ($enrollment->progress > 0)
                                                <div class="mb-3">
                                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                        <span>Прогресс</span>
                                                        <span>{{ $enrollment->progress }}%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                        <div class="bg-gray-700 h-1.5 rounded-full"
                                                             style="width: {{ $enrollment->progress }}%"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div
                                            class="mt-auto pt-3 border-t border-gray-200 space-y-1 text-sm text-gray-500">
                                            @if ($session)
                                                <div>
                                                    <span class="font-medium text-gray-700">Поток:</span>
                                                    {{ \Carbon\Carbon::parse($session->start_date)->format('d.m.Y') }}
                                                    –
                                                    {{ \Carbon\Carbon::parse($session->end_date)->format('d.m.Y') }}
                                                    <span
                                                        class="ml-1 text-xs font-medium px-1.5 py-0.5 rounded-full {{ $sessionStatus['class'] }}">{{ $sessionStatus['label'] }}</span>
                                                </div>
                                                @if ($session->location)
                                                    <div><span
                                                            class="font-medium text-gray-700">Место:</span> {{ $session->location }}
                                                    </div>
                                                @endif
                                                @if ($session->trainer)
                                                    <div><span
                                                            class="font-medium text-gray-700">Тренер:</span> {{ $session->trainer->name }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Внешние курсы</h2>

                        @if ($approvedRequests->isEmpty())
                            <p class="text-sm text-gray-500">Нет одобренных заявок на внешнее обучение.</p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($approvedRequests as $exRequest)
                                    <div class="flex flex-col bg-white px-6 py-4 rounded-sm">
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between gap-2 mb-2">
                                                <h3 class="font-semibold text-gray-900 text-base leading-snug">
                                                    {{ $exRequest->course_title }}
                                                </h3>
                                                <span
                                                    class="shrink-0 text-xs font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-800">
                                                    Одобрена
                                                </span>
                                            </div>

                                            @if ($exRequest->description)
                                                <p class="text-sm text-gray-600 mb-3">{{ $exRequest->description }}</p>
                                            @endif
                                        </div>

                                        <div
                                            class="mt-auto pt-3 border-t border-gray-200 space-y-1 text-sm text-gray-500">
                                            <div>
                                                <span class="font-medium text-gray-700">Даты:</span>
                                                {{ \Carbon\Carbon::parse($exRequest->start_date)->format('d.m.Y') }}
                                                –
                                                {{ \Carbon\Carbon::parse($exRequest->end_date)->format('d.m.Y') }}
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-700">Стоимость:</span>
                                                {{ number_format($exRequest->cost, 2, ',', ' ') }} ₽
                                            </div>
                                            @if ($exRequest->program_link)
                                                <div>
                                                    <a href="{{ $exRequest->program_link }}" target="_blank"
                                                       rel="noopener noreferrer"
                                                       class="text-gray-700 underline hover:text-gray-900">
                                                        Программа курса
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
