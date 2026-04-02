<x-app-layout>


    <x-slot:header>Главная</x-slot:header>

    <div class="py-12">
        @if (session('success'))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <div class="px-4 py-3 bg-green-100 text-green-800 rounded-sm text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(Auth::user()->hasRole('admin') && $adminStats)
                        <div class="space-y-6">
                            <div class="flex items-center justify-between gap-4 flex-wrap">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">Панель администратора</h2>
                                    <p class="text-sm text-gray-500">Ключевые показатели системы и ручные действия.</p>
                                </div>

                                <form method="POST" action="{{ route('admin.stepik.sync') }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                                        Синхронизировать курсы Stepik
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.calendar.sync') }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                                        Синхронизировать Outlook-календари
                                    </button>
                                </form>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white border border-gray-200 rounded-sm p-5">
                                    <div class="text-sm text-gray-500 mb-1">Свободное место на диске</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $adminStats['disk_free_space'] }}</div>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-sm p-5">
                                    <div class="text-sm text-gray-500 mb-1">Всего курсов</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $adminStats['total_courses'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">Внутренние: {{ $adminStats['internal_courses'] }}, Stepik: {{ $adminStats['stepik_courses'] }}</div>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-sm p-5 flex flex-col justify-between">
                                    <div>
                                        <div class="text-sm text-gray-500 mb-1">Пользователи</div>
                                        <div class="text-sm text-gray-700">Управление всеми учётными записями системы.</div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('admin.users.index') }}" class="text-gray-800 underline hover:text-black">
                                            Открыть список пользователей
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->hasRole('director') && $directorStats)
                        <div class="space-y-6">
                            <div class="flex items-center justify-between gap-4 flex-wrap">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">Панель руководителя</h2>
                                    <p class="text-sm text-gray-500">Сводная статистика по обучению в компании</p>
                                </div>
                                <div class="flex items-center gap-2 px-4 py-2 rounded-sm {{ $directorStats['overall_verdict']['class'] }}">
                                    <span class="text-lg font-bold">{{ $directorStats['overall_verdict']['icon'] }}</span>
                                    <div>
                                        <div class="text-sm font-semibold">{{ $directorStats['overall_verdict']['text'] }}</div>
                                        <div class="text-xs">Общий балл: {{ $directorStats['overall_percent'] }}%</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Основные показатели --}}
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="bg-white border border-gray-200 rounded-sm p-5">
                                    <div class="text-sm text-gray-500 mb-1">Сотрудники</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $directorStats['total_employees'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">Тренеров: {{ $directorStats['total_trainers'] }}</div>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-sm p-5">
                                    <div class="text-sm text-gray-500 mb-1">Курсы</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $directorStats['total_courses'] }}</div>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-sm p-5">
                                    <div class="text-sm text-gray-500 mb-1">Потоки</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $directorStats['total_sessions'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Активных: {{ $directorStats['active_sessions'] }},
                                        Завершённых: {{ $directorStats['completed_sessions'] }}
                                    </div>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-sm p-5">
                                    <div class="text-sm text-gray-500 mb-1">Охват обучением</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $directorStats['enrollment_rate'] }}%</div>
                                    <div class="text-xs text-gray-500 mt-1">сотрудников записаны на курсы</div>
                                </div>
                            </div>

                            {{-- Статистика записей --}}
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 mb-3">Записи на обучение</h3>
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <div class="bg-white border border-gray-200 rounded-sm p-4 text-center">
                                        <div class="text-2xl font-semibold text-gray-900">{{ $directorStats['total_enrollments'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">Всего</div>
                                    </div>
                                    <div class="bg-green-50 border border-green-200 rounded-sm p-4 text-center">
                                        <div class="text-2xl font-semibold text-green-700">{{ $directorStats['completed_enrollments'] }}</div>
                                        <div class="text-xs text-green-600 mt-1">Завершили</div>
                                    </div>
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-sm p-4 text-center">
                                        <div class="text-2xl font-semibold text-yellow-700">{{ $directorStats['in_progress_enrollments'] }}</div>
                                        <div class="text-xs text-yellow-600 mt-1">В процессе</div>
                                    </div>
                                    <div class="bg-red-50 border border-red-200 rounded-sm p-4 text-center">
                                        <div class="text-2xl font-semibold text-red-700">{{ $directorStats['failed_enrollments'] }}</div>
                                        <div class="text-xs text-red-600 mt-1">Не сдали</div>
                                    </div>
                                    <div class="bg-white border border-gray-200 rounded-sm p-4 text-center">
                                        <div class="text-2xl font-semibold text-gray-500">{{ $directorStats['cancelled_enrollments'] }}</div>
                                        <div class="text-xs text-gray-400 mt-1">Отменено</div>
                                    </div>
                                </div>

                                {{-- Прогресс-бар завершения --}}
                                <div class="mt-4 bg-white border border-gray-200 rounded-sm p-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600 font-medium">Процент завершения обучения</span>
                                        <span class="font-semibold text-gray-900">{{ $directorStats['completion_rate'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="h-3 rounded-full transition-all {{ $directorStats['completion_rate'] >= 60 ? 'bg-green-500' : ($directorStats['completion_rate'] >= 30 ? 'bg-yellow-400' : 'bg-red-400') }}"
                                             style="width: {{ $directorStats['completion_rate'] }}%"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Внешние заявки --}}
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 mb-3">Заявки на внешнее обучение</h3>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-sm p-4 text-center">
                                        <div class="text-2xl font-semibold text-yellow-700">{{ $directorStats['pending_requests'] }}</div>
                                        <div class="text-xs text-yellow-600 mt-1">На рассмотрении</div>
                                    </div>
                                    <div class="bg-green-50 border border-green-200 rounded-sm p-4 text-center">
                                        <div class="text-2xl font-semibold text-green-700">{{ $directorStats['approved_requests'] }}</div>
                                        <div class="text-xs text-green-600 mt-1">Одобрено</div>
                                    </div>
                                    <div class="bg-red-50 border border-red-200 rounded-sm p-4 text-center">
                                        <div class="text-2xl font-semibold text-red-700">{{ $directorStats['rejected_requests'] }}</div>
                                        <div class="text-xs text-red-600 mt-1">Отклонено</div>
                                    </div>
                                    <div class="bg-white border border-gray-200 rounded-sm p-4 text-center">
                                        <div class="text-2xl font-semibold text-gray-900">{{ number_format($directorStats['total_ext_budget'], 0, ',', ' ') }} ₽</div>
                                        <div class="text-xs text-gray-500 mt-1">Бюджет одобренных</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Итоговая оценка --}}
                            <div class="bg-white border border-gray-200 rounded-sm p-5">
                                <h3 class="text-base font-semibold text-gray-800 mb-3">Краткие итоги</h3>
                                <div class="space-y-2 text-sm">
                                    @if($directorStats['completion_rate'] >= 60)
                                        <div class="flex items-center gap-2 text-green-700">
                                            <span class="font-bold">✓</span>
                                            <span>Хороший уровень завершения обучения ({{ $directorStats['completion_rate'] }}%)</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-red-600">
                                            <span class="font-bold">!</span>
                                            <span>Низкий уровень завершения обучения ({{ $directorStats['completion_rate'] }}%) — необходимо усилить контроль</span>
                                        </div>
                                    @endif

                                    @if($directorStats['enrollment_rate'] >= 50)
                                        <div class="flex items-center gap-2 text-green-700">
                                            <span class="font-bold">✓</span>
                                            <span>Хороший охват сотрудников ({{ $directorStats['enrollment_rate'] }}%)</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-orange-600">
                                            <span class="font-bold">!</span>
                                            <span>Охват сотрудников обучением низкий ({{ $directorStats['enrollment_rate'] }}%) — рекомендуется расширить</span>
                                        </div>
                                    @endif

                                    @if($directorStats['failed_enrollments'] === 0)
                                        <div class="flex items-center gap-2 text-green-700">
                                            <span class="font-bold">✓</span>
                                            <span>Нет провалов — сотрудники справляются с обучением</span>
                                        </div>
                                    @elseif($directorStats['failed_enrollments'] <= 3)
                                        <div class="flex items-center gap-2 text-yellow-600">
                                            <span class="font-bold">–</span>
                                            <span>Есть {{ $directorStats['failed_enrollments'] }} несданных — стоит обратить внимание</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-red-600">
                                            <span class="font-bold">✕</span>
                                            <span>Много несданных ({{ $directorStats['failed_enrollments'] }}) — требуется анализ причин</span>
                                        </div>
                                    @endif

                                    @if($directorStats['active_sessions'] > 0)
                                        <div class="flex items-center gap-2 text-green-700">
                                            <span class="font-bold">✓</span>
                                            <span>{{ $directorStats['active_sessions'] }} активных потоков — обучение идёт</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-orange-600">
                                            <span class="font-bold">!</span>
                                            <span>Нет активных потоков — обучение приостановлено</span>
                                        </div>
                                    @endif

                                    @if($directorStats['pending_requests'] > 0)
                                        <div class="flex items-center gap-2 text-yellow-600">
                                            <span class="font-bold">–</span>
                                            <span>{{ $directorStats['pending_requests'] }} заявок на внешнее обучение ожидают рассмотрения</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->hasRole('trainer') && $trainerStats)
                        <div class="space-y-6">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Панель тренера</h2>
                                <p class="text-sm text-gray-500">Статистика по вашим курсам и потокам</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white border border-gray-200 rounded-sm p-5">
                                    <div class="text-sm text-gray-500 mb-1">Потоки</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $trainerStats['total_sessions'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Активных: {{ $trainerStats['active_sessions'] }},
                                        Завершённых: {{ $trainerStats['completed_sessions'] }}
                                    </div>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-sm p-5">
                                    <div class="text-sm text-gray-500 mb-1">Мои курсы</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $trainerStats['total_courses'] }}</div>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-sm p-5">
                                    <div class="text-sm text-gray-500 mb-1">Участники</div>
                                    <div class="text-2xl font-semibold text-gray-900">{{ $trainerStats['total_enrollments'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Завершили: {{ $trainerStats['completed_enrollments'] }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <a href="{{ route('trainer-sessions.index') }}" class="text-gray-800 underline hover:text-black text-sm">
                                    Мои потоки
                                </a>
                                <a href="{{ route('trainer-courses.index') }}" class="text-gray-800 underline hover:text-black text-sm">
                                    Мои курсы
                                </a>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->hasRole('employee'))
                        <script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.umd.js"></script>
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css" />

                        <h2 class="text-xl font-semibold text-gray-900 mb-1">Моё расписание</h2>
                        <p class="text-sm text-gray-500 mb-4">Внутренние потоки и внешние курсы</p>

                        <div class="flex flex-wrap gap-4 mb-4 text-xs">
                            <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-blue-400"></span> Запланирован</span>
                            <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-yellow-400"></span> Идёт</span>
                            <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-green-500"></span> Завершён</span>
                            <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-purple-400"></span> Внешний (одобрен)</span>
                            <span class="flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-gray-400"></span> Внешний (на рассмотрении)</span>
                        </div>

                        @if($ganttTasks->isEmpty())
                            <p class="text-sm text-gray-500">Нет данных для отображения. Запишитесь на поток или подайте заявку на внешний курс.</p>
                        @else
                            <div id="gantt"></div>
                            <script>
                                const tasks = @json($ganttTasks->values());
                                const gantt = new Gantt("#gantt", tasks, {
                                    popup: function ({ task }) {
                                        const typeLabel = task.type === 'external' ? 'Внешний курс' : 'Внутренний поток';
                                        const statusMap = {
                                            'registered': 'Записан',
                                            'in_progress': 'В процессе',
                                            'completed': 'Завершён',
                                            'failed': 'Не сдан',
                                            'cancelled': 'Отменён',
                                            'pending': 'На рассмотрении',
                                            'approved': 'Одобрена',
                                        };
                                        let html = `<div style="padding:4px 0">`;
                                        html += `<p style="font-weight:600;margin-bottom:4px">${task.name}</p>`;
                                        html += `<p>${typeLabel}</p>`;
                                        if (task.trainer) html += `<p>Тренер: ${task.trainer}</p>`;
                                        if (task.status) html += `<p>Статус: ${statusMap[task.status] || task.status}</p>`;
                                        if (task.type === 'internal' && task.progress > 0) html += `<p>Прогресс: ${task.progress}%</p>`;
                                        html += `</div>`;
                                        return html;
                                    },
                                    language: "ru",
                                    container_height: Math.max(300, tasks.length * 80),
                                    bar_height: 30,
                                    readonly: true,
                                });

                                // Обрезаем длинные названия, чтобы текст не вылезал за пределы блока
                                const ganttSvg = document.querySelector('.gantt');
                                const svgWidth = ganttSvg ? ganttSvg.getBBox().width : 1200;
                                document.querySelectorAll('.gantt .bar-wrapper').forEach(wrapper => {
                                    const bar = wrapper.querySelector('.bar');
                                    const label = wrapper.querySelector('.bar-label');
                                    if (!bar || !label) return;
                                    const barBox = bar.getBBox();
                                    const barWidth = barBox.width;
                                    const isBig = label.classList.contains('big');
                                    const maxTextWidth = isBig
                                        ? svgWidth - barBox.x - barWidth - 20
                                        : barWidth - 10;
                                    if (maxTextWidth <= 20) { label.textContent = ''; return; }
                                    let text = label.textContent;
                                    label.textContent = text;
                                    while (label.getComputedTextLength() > maxTextWidth && text.length > 0) {
                                        text = text.slice(0, -1);
                                        label.textContent = text + '…';
                                    }
                                });
                            </script>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <style>
            .gantt-container .popup-wrapper {
                width: 200px!important;
                padding: 0 5px!important;
            }
            .bar-planned .bar-progress, .bar-planned .bar { fill: #60a5fa; }
            .bar-ongoing .bar-progress, .bar-ongoing .bar { fill: #facc15; }
            .bar-completed .bar-progress, .bar-completed .bar { fill: #22c55e; }
            .bar-approved .bar-progress, .bar-approved .bar { fill: #a78bfa; }
            .bar-pending .bar-progress, .bar-pending .bar { fill: #9ca3af; }
            .gantt .bar-wrapper .bar-label { font-size: 13px; }
            .gantt .bar-wrapper .bar-label.big { x: 5; dominant-baseline: central; }
        </style>

    </div>
</x-app-layout>
