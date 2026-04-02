@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot:header>{{ $enrollment->session?->course?->title ?? 'Курс' }}</x-slot:header>

    <div class="py-12 px-12">
        <div class="max-w-4xl">
            <div class="bg-white">
                <div class="p-6 text-gray-900 space-y-6">

                    <div>
                        <a href="{{ route('my-courses.index') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">&larr; Назад к моим курсам</a>
                    </div>

                    @php
                        $session = $enrollment->session;
                        $course = $session?->course;
                    @endphp

                    {{-- Информация о потоке --}}
                    <div class="bg-white px-6 py-4 rounded-sm space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Курс:</span>
                                <p>{{ $course?->title ?? 'Курс удалён' }}</p>
                            </div>
                            @if ($course?->direction)
                                <div>
                                    <span class="font-medium text-gray-700">Направление:</span>
                                    <p>{{ $course->direction->name }}</p>
                                </div>
                            @endif
                            @if ($session)
                                <div>
                                    <span class="font-medium text-gray-700">Даты потока:</span>
                                    <p>{{ Carbon::parse($session->start_date)->format('d.m.Y') }}
                                        – {{ Carbon::parse($session->end_date)->format('d.m.Y') }}</p>
                                </div>
                            @endif
                            @if ($session?->trainer)
                                <div>
                                    <span class="font-medium text-gray-700">Тренер:</span>
                                    <p>{{ $session->trainer->name }}</p>
                                </div>
                            @endif
                            @if ($session?->location)
                                <div>
                                    <span class="font-medium text-gray-700">Место:</span>
                                    <p>{{ $session->location }}</p>
                                </div>
                            @endif
                        </div>
                        @if ($course?->description)
                            <div class="text-sm">
                                <span class="font-medium text-gray-700">Описание:</span>
                                <p class="mt-1 text-gray-600">{{ $course->description }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Список уроков --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-800 mb-4">
                            Уроки ({{ $course?->lessons?->count() ?? 0 }})
                        </h2>

                        @if ($course?->lessons->isEmpty())
                            <p class="text-sm text-gray-500">В этом курсе пока нет уроков.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($course->lessons as $index => $lesson)
                                    <div class="bg-white px-6 py-4 rounded-sm border border-gray-200">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-medium text-gray-400">{{ $index + 1 }}.</span>
                                            <span class="font-medium text-gray-900">{{ $lesson->title }}</span>
                                        </div>
                                        @if ($lesson->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $lesson->description }}</p>
                                        @endif
                                        @if ($lesson->link)
                                            <a href="{{ $lesson->link }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mt-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                Перейти к уроку
                                            </a>
                                        @endif
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
