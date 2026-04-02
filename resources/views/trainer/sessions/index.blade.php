<x-app-layout>
    <x-slot:header>Мои потоки</x-slot:header>

    <div class="py-12 px-12">
        <div class="">
            <div class="bg-white">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-sm text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <p class="text-sm text-gray-600">Управляйте своими потоками обучения</p>
                        <a href="{{ route('trainer-sessions.create') }}"
                           class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                            + Новый поток
                        </a>
                    </div>

                    @if ($sessions->isEmpty())
                        <p class="text-gray-500 text-sm">У вас ещё нет потоков.</p>
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
                                <a href="{{ route('trainer-sessions.show', $session) }}"
                                   class="block bg-white px-6 py-4 rounded-sm hover:bg-gray-100 transition-colors border border-gray-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900">{{ $session->course->title }}</div>
                                            <div class="text-sm text-gray-500 mt-1 space-x-3">
                                                <span>{{ \Carbon\Carbon::parse($session->start_date)->format('d.m.Y') }} – {{ \Carbon\Carbon::parse($session->end_date)->format('d.m.Y') }}</span>
                                                <span class="inline-block">{{ $session->enrollments->count() }} участников</span>
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
