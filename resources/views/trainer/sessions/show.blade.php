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
            <div class="bg-slate-200">
                <div class="p-6 text-gray-900 space-y-6">

                    <div>
                        <a href="{{ route('trainer-sessions.index') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">&larr; Назад к потокам</a>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 rounded-sm space-y-3">
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
                            @if ($session->location)
                                <div>
                                    <span class="font-medium text-gray-700">Место:</span>
                                    <p>{{ $session->location }}</p>
                                </div>
                            @endif
                            @if ($session->start_time && $session->end_time)
                                <div>
                                    <span class="font-medium text-gray-700">Время:</span>
                                    <p>{{ $session->start_time }} – {{ $session->end_time }}</p>
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
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Участник
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Статус
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Прогресс
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Оценка
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Сертификат
                                        </th>
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
                                                <div
                                                    class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $enrollment->user->email }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <form method="POST"
                                                      action="{{ route('trainer-enrollments.update-status', $enrollment) }}"
                                                      class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" onchange="this.form.submit()"
                                                            class="text-xs border-gray-300 rounded px-2 py-1 focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option
                                                            value="registered" @selected($enrollment->status === 'registered')>
                                                            Записан
                                                        </option>
                                                        <option
                                                            value="in_progress" @selected($enrollment->status === 'in_progress')>
                                                            В процессе
                                                        </option>
                                                        <option
                                                            value="completed" @selected($enrollment->status === 'completed')>
                                                            Завершён
                                                        </option>
                                                        <option
                                                            value="failed" @selected($enrollment->status === 'failed')>
                                                            Не сдан
                                                        </option>
                                                        <option
                                                            value="cancelled" @selected($enrollment->status === 'cancelled')>
                                                            Отменён
                                                        </option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-700">{{ $enrollment->progress }}%</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-700">
                                                    {{ $enrollment->score ? $enrollment->score : '—' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($enrollment->status === 'completed' || $enrollment->status === 'failed')
                                                    @if ($enrollment->certificate_path)
                                                        <a href="{{ Storage::url($enrollment->certificate_path) }}"
                                                           target="_blank"
                                                           class="text-xs text-indigo-600 hover:text-indigo-800 underline">
                                                            Просмотр
                                                        </a>
                                                    @else
                                                        <button type="button"
                                                                onclick="openCertificateModal({{ $enrollment->id }})"
                                                                class="text-xs text-indigo-600 hover:text-indigo-800 underline">
                                                            Загрузить
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="text-xs text-gray-500">—</span>
                                                @endif
                                            </td>
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
                        @if ($availableEmployees->isNotEmpty())
                            <form method="POST" action="{{ route('trainer-sessions.enroll', $session) }}" class="flex items-end gap-3">
                                @csrf
                                <div class="flex-1">
                                    <select name="user_id" required
                                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">-- Выберите сотрудника --</option>
                                        @foreach ($availableEmployees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->email }})</option>
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

    <div id="certificateModal" style="display: none;"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Загрузить сертификат</h3>
            <form id="certificateForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Файл сертификата</label>
                    <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700"
                           required>
                    <p class="text-xs text-gray-500 mt-1">PDF, JPG или PNG (макс. 10 МБ)</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700">
                        Загрузить
                    </button>
                    <button type="button" onclick="closeCertificateModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-sm hover:bg-gray-50">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCertificateModal(enrollmentId) {
            const modal = document.getElementById('certificateModal');
            const form = document.getElementById('certificateForm');
            form.action = `/trainer/enrollments/${enrollmentId}/certificate`;
            modal.style.display = 'flex';
        }

        function closeCertificateModal() {
            document.getElementById('certificateModal').style.display = 'none';
        }

        document.addEventListener('click', function (event) {
            const modal = document.getElementById('certificateModal');
            if (event.target === modal) {
                closeCertificateModal();
            }
        });
    </script>
</x-app-layout>
