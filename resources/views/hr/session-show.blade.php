@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot:header>{{ $session->course->title }}</x-slot:header>

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

        <div class="max-w-4xl">
            <div class="bg-white">
                <div class="p-6 text-gray-900 space-y-6">

                    <div>
                        <a href="{{ route('hr.sessions') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">&larr; Назад к потокам</a>
                    </div>

                    <div class="bg-white px-6 py-4 rounded-sm space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Курс:</span>
                                <p>{{ $session->course->title }}</p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Даты:</span>
                                <p>{{ Carbon::parse($session->start_date)->format('d.m.Y') }}
                                    – {{ Carbon::parse($session->end_date)->format('d.m.Y') }}</p>
                            </div>
                            @if ($session->trainer)
                                <div>
                                    <span class="font-medium text-gray-700">Тренер:</span>
                                    <p>{{ $session->trainer->name }}</p>
                                </div>
                            @endif
                            @if ($session->location)
                                <div>
                                    <span class="font-medium text-gray-700">Место:</span>
                                    <p>{{ $session->location }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-gray-800 mb-4">
                            Участники ({{ $enrollments->count() }})
                        </h2>

                        @if ($enrollments->isEmpty())
                            <p class="text-sm text-gray-500">На этот поток пока никто не записан.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-white">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Участник</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Прогресс</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Оценка</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($enrollments as $enrollment)
                                            @php
                                                $statusLabel = match($enrollment->status) {
                                                    'registered' => ['text' => 'Записан', 'class' => 'bg-gray-100 text-gray-600'],
                                                    'in_progress' => ['text' => 'В процессе', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                    'completed' => ['text' => 'Завершён', 'class' => 'bg-green-100 text-green-800'],
                                                    'failed' => ['text' => 'Не сдан', 'class' => 'bg-red-100 text-red-800'],
                                                    'cancelled' => ['text' => 'Отменён', 'class' => 'bg-gray-100 text-gray-500'],
                                                    default => ['text' => $enrollment->status, 'class' => 'bg-gray-100 text-gray-600'],
                                                };
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $enrollment->user->email }}</div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="text-xs font-medium px-2 py-1 rounded-full {{ $statusLabel['class'] }}">
                                                        {{ $statusLabel['text'] }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $enrollment->progress }}%</td>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $enrollment->score ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Записать сотрудника --}}
                    <div class="border-t border-gray-300 pt-6">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Записать сотрудника</h3>
                        @if ($availableUsers->isNotEmpty())
                            <form method="POST" action="{{ route('hr.sessions.enroll', $session) }}" class="flex items-end gap-3">
                                @csrf
                                <div class="flex-1">
                                    <select name="user_id" required
                                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">-- Выберите сотрудника --</option>
                                        @foreach ($availableUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
                                </div>
                                <x-primary-button>Записать</x-primary-button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500">Все пользователи уже записаны на этот поток.</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
