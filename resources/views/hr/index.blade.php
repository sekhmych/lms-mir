<x-app-layout>
    <x-slot:header>HR-панель</x-slot:header>

    <div class="py-12 px-12">
        <div class="">
            <div class="bg-slate-200">
                <div class="p-6 text-gray-900 space-y-8">

                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Статистика обучения</h2>
                            <p class="text-sm text-gray-500">Сводные данные по всем обучениям в организации</p>
                        </div>
                        <a href="{{ route('hr.export') }}"
                           class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                            Скачать отчёт Excel
                        </a>
                    </div>

                    {{-- Люди --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Персонал</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Сотрудники</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['totalEmployees'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Тренеры</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['totalTrainers'] }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Курсы --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Курсы</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Всего курсов</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['totalCourses'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Внутренние</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['internalCourses'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Внешние</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['externalCourses'] }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Потоки --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Потоки</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Всего потоков</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['totalSessions'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Активные</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['activeSessions'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Завершённые</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['completedSessions'] }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Записи на обучения --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Записи на внутренние обучения</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Всего записей</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['totalEnrollments'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">В процессе</div>
                                <div class="text-2xl font-semibold text-yellow-700">{{ $stats['inProgressEnrollments'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Завершили</div>
                                <div class="text-2xl font-semibold text-green-700">{{ $stats['completedEnrollments'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Не сдали</div>
                                <div class="text-2xl font-semibold text-red-700">{{ $stats['failedEnrollments'] }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Внешние заявки --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">Заявки на внешнее обучение</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Всего заявок</div>
                                <div class="text-2xl font-semibold text-gray-900">{{ $stats['totalExternalRequests'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Ожидают рассмотрения</div>
                                <div class="text-2xl font-semibold text-yellow-700">{{ $stats['pendingExternalRequests'] }}</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                                <div class="text-sm text-gray-500 mb-1">Одобрены</div>
                                <div class="text-2xl font-semibold text-green-700">{{ $stats['approvedExternalRequests'] }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Быстрые ссылки --}}
                    <div class="flex flex-wrap gap-4 pt-2">
                        <a href="{{ route('hr.external-requests') }}" class="text-gray-800 underline hover:text-black text-sm">
                            Рассмотрение заявок
                        </a>
                        <a href="{{ route('hr.sessions') }}" class="text-gray-800 underline hover:text-black text-sm">
                            Потоки обучения
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
