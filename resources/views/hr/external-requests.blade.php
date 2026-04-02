<x-app-layout>
    <x-slot:header>Заявки на внешнее обучение</x-slot:header>

    <div class="py-12 px-12">
        <div class="">
            <div class="bg-white">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-sm text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <a href="{{ route('hr.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Назад к HR-панели</a>
                    </div>

                    @if ($requests->isEmpty())
                        <p class="text-sm text-gray-500">Заявок пока нет.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сотрудник</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Курс</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Даты</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Стоимость</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($requests as $extRequest)
                                        @php
                                            $statusLabel = match($extRequest->status) {
                                                'pending' => ['text' => 'На рассмотрении', 'class' => 'bg-yellow-100 text-yellow-800'],
                                                'approved' => ['text' => 'Одобрена', 'class' => 'bg-green-100 text-green-800'],
                                                'rejected' => ['text' => 'Отклонена', 'class' => 'bg-red-100 text-red-800'],
                                                'completed' => ['text' => 'Завершена', 'class' => 'bg-gray-100 text-gray-600'],
                                                default => ['text' => $extRequest->status, 'class' => 'bg-gray-100 text-gray-600'],
                                            };
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $extRequest->user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $extRequest->user->email }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900">{{ $extRequest->course_title }}</div>
                                                @if ($extRequest->program_link)
                                                    <a href="{{ $extRequest->program_link }}" target="_blank" rel="noopener noreferrer"
                                                       class="text-xs text-indigo-600 hover:text-indigo-800 underline">Программа</a>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ \Carbon\Carbon::parse($extRequest->start_date)->format('d.m.Y') }}
                                                – {{ \Carbon\Carbon::parse($extRequest->end_date)->format('d.m.Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ number_format($extRequest->cost, 2, ',', ' ') }} ₽
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-xs font-medium px-2 py-1 rounded-full {{ $statusLabel['class'] }}">
                                                    {{ $statusLabel['text'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($extRequest->status === 'pending')
                                                    <div class="flex gap-2">
                                                        <form method="POST" action="{{ route('hr.external-requests.update', $extRequest) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit"
                                                                class="text-xs px-3 py-1 bg-green-600 text-white rounded-sm hover:bg-green-700">
                                                                Одобрить
                                                            </button>
                                                        </form>
                                                        <button type="button"
                                                            onclick="openRejectModal({{ $extRequest->id }})"
                                                            class="text-xs px-3 py-1 bg-red-600 text-white rounded-sm hover:bg-red-700">
                                                            Отклонить
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $requests->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Модальное окно отклонения --}}
    <div id="rejectModal" style="display: none;"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Отклонить заявку</h3>
            <form id="rejectForm" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Комментарий (необязательно)</label>
                    <textarea name="comment" rows="3"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                        placeholder="Причина отклонения..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-sm hover:bg-red-700">
                        Отклонить
                    </button>
                    <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-sm hover:bg-gray-50">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(id) {
            const form = document.getElementById('rejectForm');
            form.action = `/hr/external-requests/${id}`;
            document.getElementById('rejectModal').style.display = 'flex';
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
        document.addEventListener('click', function (e) {
            if (e.target === document.getElementById('rejectModal')) closeRejectModal();
        });
    </script>
</x-app-layout>
