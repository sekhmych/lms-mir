<x-app-layout>
    <x-slot:header>{{ $course->title }}</x-slot:header>

    <div class="py-12 px-12">

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-sm text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 px-4 py-3 bg-yellow-100 text-yellow-800 rounded-sm text-sm">
                {{ session('warning') }}
            </div>
        @endif

        <div class="max-w-2xl">

            <div class="bg-white">

                <div class="p-6 text-gray-900">
                    <div class="bg-white px-6 py-4 rounded-sm space-y-4">

                        <div class="">
                            <a href="{{ route('courses.index') }}"
                                class="text-sm text-gray-500">&larr;
                                Назад к
                                курсам</a>
                        </div>

                        @if ($course->description)
                            <p class="text-gray-600">{{ $course->description }}</p>
                        @endif

                        <div class="space-y-1 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Направление:</span>
                                {{ $course->direction?->name ?? 'Без направления' }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Объём:</span>
                                {{ $course->duration ? $course->duration . ' ч' : 'n/a' }}
                            </div>
                        </div>
                    </div>

                    {{-- Список потоков --}}
                    <div class="mt-6">
                        <h2 class="text-base font-semibold text-gray-800 mb-4">Доступные потоки</h2>
                        @if ($course->sessions->isEmpty())
                            <p class="text-sm text-gray-500">Нет доступных потоков для этого курса.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($course->sessions as $session)
                                    @php
                                        $statusLabel = match($session->status) {
                                            'planned' => ['text' => 'Запланирован', 'class' => 'bg-blue-100 text-blue-700'],
                                            'ongoing' => ['text' => 'Идёт', 'class' => 'bg-yellow-100 text-yellow-800'],
                                            'completed' => ['text' => 'Завершён', 'class' => 'bg-gray-100 text-gray-600'],
                                            default => ['text' => $session->status, 'class' => 'bg-gray-100 text-gray-600'],
                                        };
                                        $isEnrolled = \App\Models\Enrollment::query()
                                            ->where('user_id', auth()->id())
                                            ->where('course_id', $course->id)
                                            ->exists();
                                    @endphp
                                    <div class="bg-white border border-gray-200 rounded-sm p-4">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ \Carbon\Carbon::parse($session->start_date)->format('d.m.Y') }}
                                                        –
                                                        {{ \Carbon\Carbon::parse($session->end_date)->format('d.m.Y') }}
                                                    </span>
                                                    <span class="text-xs font-medium px-2 py-1 rounded-full {{ $statusLabel['class'] }}">
                                                        {{ $statusLabel['text'] }}
                                                    </span>
                                                </div>
                                                <div class="space-y-1 text-sm text-gray-600">
                                                    @if ($session->location)
                                                        <div><span class="font-medium text-gray-700">Место:</span> {{ $session->location }}</div>
                                                    @endif
                                                    @if ($session->start_time && $session->end_time)
                                                        <div><span class="font-medium text-gray-700">Время:</span> {{ $session->start_time }} – {{ $session->end_time }}</div>
                                                    @endif
                                                    @if ($session->trainer)
                                                        <div><span class="font-medium text-gray-700">Тренер:</span> {{ $session->trainer->name }}</div>
                                                    @endif
                                                    @if ($session->max_participants)
                                                        <div><span class="font-medium text-gray-700">Макс. участников:</span> {{ $session->max_participants }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if (! $isEnrolled)
                                                <form method="POST" action="{{ route('enrollments.store', $session) }}" class="sm:shrink-0">
                                                    @csrf
                                                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                                                        Записаться
                                                    </button>
                                                </form>
                                            @else
                                                <div class="px-4 py-2 bg-green-100 text-green-800 text-sm rounded-sm font-medium text-center">
                                                    ✓ Записаны
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