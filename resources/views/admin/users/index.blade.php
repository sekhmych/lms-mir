<x-app-layout>
    <x-slot:header>Пользователи</x-slot:header>

    <div class="py-12 px-12" x-data="{
        calendarOpen: false,
        calendarUser: '',
        calendarEvents: [],
        calendarLoading: false,
        calendarError: '',
        async openCalendar(userId, userName) {
            this.calendarUser = userName;
            this.calendarEvents = [];
            this.calendarError = '';
            this.calendarLoading = true;
            this.calendarOpen = true;
            try {
                const res = await fetch(`/admin/users/${userId}/calendar`);
                const data = await res.json();
                if (data.error) {
                    this.calendarError = data.error;
                } else {
                    this.calendarEvents = data.events || [];
                }
            } catch (e) {
                this.calendarError = 'Ошибка загрузки календаря.';
            }
            this.calendarLoading = false;
        },
        formatDate(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            return d.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },
        formatTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            return d.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
        }
    }">
        <div class="bg-white">
            <div class="p-6 text-gray-900 space-y-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Все пользователи</h1>
                        <p class="text-sm text-gray-500">Управление учётными записями и ролями.</p>
                    </div>
                    <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                        Создать пользователя
                    </a>
                </div>

                @if (session('success'))
                    <div class="px-4 py-3 bg-green-100 text-green-800 rounded-sm text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white border border-slate-300 rounded-sm overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-3 font-medium">Имя</th>
                                <th class="px-4 py-3 font-medium">Email</th>
                                <th class="px-4 py-3 font-medium">Должность</th>
                                <th class="px-4 py-3 font-medium">Роль</th>
                                <th class="px-4 py-3 font-medium">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->position ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->roles->pluck('name')->implode(', ') ?: '—' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-800 underline hover:text-black">
                                                Редактировать
                                            </a>

                                            @if (in_array($user->id, $msGraphUserIds))
                                                <button type="button"
                                                        @click="openCalendar({{ $user->id }}, '{{ e($user->name) }}')"
                                                        class="text-blue-600 underline hover:text-blue-800">
                                                    Календарь
                                                </button>
                                            @endif

                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Удалить пользователя {{ $user->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 underline hover:text-red-700">
                                                    Удалить
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        {{-- Модальное окно календаря --}}
        <div x-show="calendarOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
             style="display: none;">
            <div class="fixed inset-0 bg-gray-500 opacity-75" @click="calendarOpen = false"></div>
            <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform sm:w-full sm:max-w-3xl sm:mx-auto relative"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Календарь Outlook — <span x-text="calendarUser"></span></h2>
                        <button @click="calendarOpen = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div x-show="calendarLoading" class="text-sm text-gray-500 py-8 text-center">Загрузка событий…</div>
                    <div x-show="calendarError" class="text-sm text-red-600 py-4" x-text="calendarError"></div>

                    <div x-show="!calendarLoading && !calendarError && calendarEvents.length === 0" class="text-sm text-gray-500 py-8 text-center">
                        Нет предстоящих событий.
                    </div>

                    <div x-show="!calendarLoading && !calendarError && calendarEvents.length > 0" class="max-h-96 overflow-y-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-left text-gray-600 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 font-medium">Событие</th>
                                    <th class="px-4 py-2 font-medium">Дата</th>
                                    <th class="px-4 py-2 font-medium">Время</th>
                                    <th class="px-4 py-2 font-medium">Место</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <template x-for="event in calendarEvents" :key="event.id">
                                    <tr>
                                        <td class="px-4 py-2 font-medium text-gray-900">
                                            <a x-show="event.webLink" :href="event.webLink" target="_blank" class="text-blue-600 hover:underline" x-text="event.subject"></a>
                                            <span x-show="!event.webLink" x-text="event.subject"></span>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600" x-text="formatDate(event.start?.dateTime)"></td>
                                        <td class="px-4 py-2 text-gray-600">
                                            <span x-show="!event.isAllDay" x-text="formatTime(event.start?.dateTime) + ' — ' + formatTime(event.end?.dateTime)"></span>
                                            <span x-show="event.isAllDay" class="text-gray-400">Весь день</span>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600" x-text="event.location?.displayName || '—'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>