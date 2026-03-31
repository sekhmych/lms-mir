<x-app-layout>
    <x-slot:header>Курсы</x-slot:header>

    <div class="py-12 px-12">
        <div class="">
            <div class="bg-slate-200">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($courses as $course)
                            <a href="{{ route('courses.show', $course) }}" class="flex flex-col bg-gray-50 px-6 py-4 rounded-sm hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 text-base leading-snug mb-2">{{ $course->title }}</h3>
                                    @if ($course->description)
                                        <p class="text-sm text-gray-600 mb-4">{{ $course->description }}</p>
                                    @endif
                                </div>

                                {{-- Информация о потоках --}}
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    @if ($course->sessions->isNotEmpty())
                                        <div class="text-xs text-gray-700 mb-2 font-medium">Доступные потоки:</div>
                                        <div class="space-y-1 text-xs text-gray-600">
                                            @foreach ($course->sessions->take(2) as $session)
                                                <div class="flex items-center justify-between">
                                                    <span>
                                                        {{ \Carbon\Carbon::parse($session->start_date)->format('d.m') }}
                                                        –
                                                        {{ \Carbon\Carbon::parse($session->end_date)->format('d.m.Y') }}
                                                    </span>
                                                    @php
                                                        $statusLabel = match($session->status) {
                                                            'planned' => ['text' => 'Запланирован', 'class' => 'bg-blue-100 text-blue-700'],
                                                            'ongoing' => ['text' => 'Идёт', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                            'completed' => ['text' => 'Завершён', 'class' => 'bg-gray-100 text-gray-600'],
                                                            default => ['text' => $session->status, 'class' => 'bg-gray-100 text-gray-600'],
                                                        };
                                                    @endphp
                                                    <span class="px-1.5 py-0.5 rounded-full {{ $statusLabel['class'] }} font-medium">{{ $statusLabel['text'] }}</span>
                                                </div>
                                            @endforeach
                                            @if ($course->sessions->count() > 2)
                                                <div class="text-gray-500 text-xs pt-1">+{{ $course->sessions->count() - 2 }} ещё</div>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-400 italic">Нет активных потоков</p>
                                    @endif
                                </div>

                                {{-- Основная информация --}}
                                <div class="mt-3 pt-3 border-t border-gray-200 space-y-0.5">
                                    <div><span class="font-medium text-gray-700 text-sm">Направление:</span> <span class="text-sm">{{ $course->direction?->name ?? 'Без направления' }}</span></div>
                                    <div><span class="font-medium text-gray-700 text-sm">Объём:</span> <span class="text-sm">{{ $course->duration ? $course->duration.' ч' : 'n/a' }}</span></div>
                                </div>
                            </a>
                        @empty
                            <p class="text-gray-500">Сейчас нет доступных внутренних курсов.</p>
                        @endforelse
                    </div>

                        <div class="mt-6">
                            {{ $courses->links() }}
                        </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
