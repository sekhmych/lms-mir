<x-guest-layout>

    <div class="flex flex-col space-y-6">

        <div class="text-center">
            <h1 class="text-2xl font-medium text-gray-800">Добро пожаловать в LMS-Мир</h1>
            <p class="mt-1 text-gray-500 text-sm">Это корпоративная платформа для обучения персонала и сбора аналитических данных</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center text-sm text-gray-600">
            <div class="flex flex-col items-center space-y-1">
                <span class="font-semibold text-gray-700">Курсы</span>
                <span>Внутренние и внешние курсы с получением сертификатов</span>
            </div>
            <div class="flex flex-col items-center space-y-1">
                <span class="font-semibold text-gray-700">Расписание</span>
                <span>Диаграмма Ганта и интеграция с Microsoft Outlook</span>
            </div>
            <div class="flex flex-col items-center space-y-1">
                <span class="font-semibold text-gray-700">Аналитика</span>
                <span>Формирование отчётностей по обучению персонала</span>
            </div>
        </div>

        <div class="flex flex-col items-center space-y-3">
            <a href="/login"
               class="w-full sm:w-auto px-8 py-2.5 bg-blue-500 hover:bg-blue-400  text-white font-semibold rounded-sm text-center">
                Войти в систему
            </a>
        </div>

    </div>

</x-guest-layout>