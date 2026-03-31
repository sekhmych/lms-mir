<x-app-layout>


    <x-slot:header>Главная</x-slot:header>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(Auth::user()->hasRole('employee'))
                        <script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.umd.js"></script>
                        <link
                            rel="stylesheet"
                            href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css"
                        />
                        <div id="gantt" class=""></div>
                        <script>
                            let lessons = [
                                {
                                    id: '1',
                                    name: 'Моя профессия ИТ 25-26',
                                    trainer: 'Тимофей',
                                    start: '2026-03-30',
                                    end: '2026-04-04',
                                    progress: 33
                                },
                                {
                                    id: '2',
                                    name: 'Реляционные базы данных',
                                    title: 'wefwef',
                                    trainer: 'Лэби',
                                    start: '2026-04-16',
                                    end: '2026-04-24',
                                    progress: 5
                                },
                                {
                                    id: '3',
                                    name: 'VibeCodding. Плюсы и Минусы. Лекция',
                                    trainer: 'Гендальф',
                                    title: 'wefwef',
                                    start: '2026-04-01',
                                    end: '2026-04-08',
                                    progress: 5
                                },
                            ]

                            let gantt = new Gantt("#gantt", lessons, {
                                popup: function (task) {
                                    return `
                                        <div>
                                            <p>Наименование занятия: ${task.task.name}</p>
                                            <p>Тренер: ${task.task.trainer}</p>
                                            <a class="text-blue-500 underline" href="/course/142/lesson/4">Ссылка на урок</a>
                                        </div>
                                    `;
                                },
                                language: "ru",
                                container_height: "250",
                                readonly: true,
                            });
                        </script>
                    @endif
                </div>
            </div>
        </div>

        <style>
            gantt-container .popup-wrapper {
                width: 200px!important;
                padding: 0 5px!important;
            }
        </style>

    </div>
</x-app-layout>
