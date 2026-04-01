<x-app-layout>
    <x-slot:header>Потоки обучения</x-slot:header>

    <div class="py-12 px-12">
        <div class="">
            <div class="bg-slate-200">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('hr.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Назад к HR-панели</a>
                    </div>

                    @if ($sessions->isEmpty())
                        <p class="text-sm text-gray-500">Потоков пока нет.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($sessions as $session)
                                @php
                                    $statusLabel = match($session->status) {
                                        'planned' => ['text' => 'Запланирован', 'class' => 'bg-blue-100 text-blue-700'],
                                        'ongoing' => ['text' => 'Идёт', 'class' => 'bg-yellow-100 text-yellow-800'],
                                        'completed' => ['text' => 'Завершён', 'class' => 'bg-gray-100 text-gray-600'],
                                        'cancelled' => ['text' => 'Отменён', 'class' => 'bg-red-100 text-red-600'],
                                        default => ['text' => $session->status, 'class' => 'bg-gray-100 text-gray-600'],
                                    };
                                @endphp
                                <a href="{{ route('hr.sessions.show', $session) }}"
                                   class="block bg-gray-50 px-6 py-4 rounded-sm hover:bg-gray-100 transition-colors border border-gray-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900">{{ $session->course->title }}</div>
                                            <div class="text-sm text-gray-500 mt-1 space-x-3">
                                                <span>{{ \Carbon\Carbon::parse($session->start_date)->format('d.m.Y') }} – {{ \Carbon\Carbon::parse($session->end_date)->format('d.m.Y') }}</span>
                                                @if ($session->trainer)
                                                    <span>Тренер: {{ $session->trainer->name }}</span>
                                                @endif
                                                <span>{{ $session->enrollments->count() }} участников</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-medium px-2.5 py-1 rounded-full whitespace-nowrap {{ $statusLabel['class'] }}">
                                                {{ $statusLabel['text'] }}
                                            </span>
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $sessions->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
