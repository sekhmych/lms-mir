<x-app-layout>
    <x-slot:header>Курсы</x-slot:header>

    <div class="py-12 px-12">
        <div class="bg-slate-200">
            <div class="p-6 text-gray-900 space-y-6">
                <div class="bg-white rounded-sm border border-slate-300 p-4">
                    <form method="GET" action="{{ route('courses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Тип</label>
                            <select id="type" name="type" class="w-full border-gray-300 rounded-sm text-sm focus:border-gray-400 focus:ring-gray-400">
                                <option value="all" @selected($filters['type'] === 'all')>Все курсы</option>
                                <option value="internal" @selected($filters['type'] === 'internal')>Внутренние</option>
                                <option value="external" @selected($filters['type'] === 'external')>Внешние (Stepik)</option>
                            </select>
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Стоимость</label>
                            <select id="price" name="price" class="w-full border-gray-300 rounded-sm text-sm focus:border-gray-400 focus:ring-gray-400">
                                <option value="all" @selected($filters['price'] === 'all')>Любая</option>
                                <option value="free" @selected($filters['price'] === 'free')>Бесплатно</option>
                                <option value="paid" @selected($filters['price'] === 'paid')>Платные</option>
                            </select>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Категория Stepik</label>
                            <select id="category" name="category" class="w-full border-gray-300 rounded-sm text-sm focus:border-gray-400 focus:ring-gray-400">
                                <option value="">Все категории</option>
                                @foreach ($stepikCategories as $category)
                                    <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                                Применить
                            </button>
                            <a href="{{ route('courses.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-sm rounded-sm hover:bg-gray-50 transition-colors">
                                Сбросить
                            </a>
                        </div>
                    </form>

                    <div class="mt-3 text-xs text-gray-500">
                        Внешние курсы берутся из Stepik и сохраняются локально в базе данных после синхронизации.
                    </div>
                </div>

                @php
                    $hasCourses = count($courses) > 0;
                @endphp

                @if (! $hasCourses)
                    <div class="bg-white rounded-sm border border-dashed border-slate-300 p-8 text-center text-gray-500">
                        По выбранным фильтрам курсы не найдены.
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($courses as $course)
                            @if ($course->source_type === 'internal')
                                <a href="{{ route('courses.show', $course) }}" class="flex flex-col bg-gray-50 px-6 py-4 rounded-sm hover:bg-gray-100 transition-colors border border-transparent hover:border-gray-200">
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-800 text-white">Внутренний</span>
                                        <span class="text-xs text-gray-500">
                                            {{ $course->price && $course->price > 0 ? number_format($course->price, 0, ',', ' ') . ' ₽' : 'Бесплатно' }}
                                        </span>
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 text-base leading-snug mb-2">{{ $course->title }}</h3>
                                        @if ($course->description)
                                            <p class="text-sm text-gray-600 mb-4">{{ \Illuminate\Support\Str::limit($course->description, 140) }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        @if ($course->sessions->isNotEmpty())
                                            <div class="text-xs text-gray-700 mb-2 font-medium">Доступные потоки:</div>
                                            <div class="space-y-1 text-xs text-gray-600">
                                                @foreach ($course->sessions->take(2) as $session)
                                                    <div class="flex items-center justify-between gap-2">
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

                                    <div class="mt-3 pt-3 border-t border-gray-200 space-y-0.5">
                                        <div><span class="font-medium text-gray-700 text-sm">Направление:</span> <span class="text-sm">{{ $course->direction?->name ?? 'Без направления' }}</span></div>
                                        <div><span class="font-medium text-gray-700 text-sm">Объём:</span> <span class="text-sm">{{ $course->duration ? $course->duration.' ч' : 'n/a' }}</span></div>
                                    </div>
                                </a>
                            @else
                                <div class="flex flex-col bg-white rounded-sm border border-slate-300 overflow-hidden">
                                    @if ($course->cover_url)
                                        <img src="{{ $course->cover_url }}" alt="{{ $course->title }}" class="w-full h-44 object-cover">
                                    @endif

                                    <div class="p-5 flex flex-col flex-1">
                                        <div class="flex items-center justify-between gap-3 mb-3">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Stepik</span>
                                            <span class="text-xs text-gray-500">{{ $course->is_paid ? ($course->display_price ?: 'Платный') : 'Бесплатно' }}</span>
                                        </div>

                                        <h3 class="font-semibold text-gray-900 text-base leading-snug mb-2">{{ $course->title }}</h3>

                                        @if ($course->summary)
                                            <p class="text-sm text-gray-600 mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($course->summary), 150) }}</p>
                                        @endif

                                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                                            @if ($course->categories)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach (array_slice($course->categories, 0, 3) as $category)
                                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-700">{{ $category }}</span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="flex items-center justify-between gap-3 text-xs text-gray-500">
                                                <span>{{ number_format($course->learners_count, 0, ',', ' ') }} слушателей</span>
                                                @if ($course->difficulty)
                                                    <span>Сложность: {{ $course->difficulty }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-auto pt-4 border-t border-slate-200">
                                            <a href="{{ $course->course_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full justify-center px-4 py-2 bg-blue-600 text-white text-sm rounded-sm hover:bg-blue-700 transition-colors">
                                                Открыть на Stepik
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if ($courses->hasPages())
                    <div class="mt-6">
                        {{ $courses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
