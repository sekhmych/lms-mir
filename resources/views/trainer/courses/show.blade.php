<x-app-layout>
    <x-slot:header>{{ $course->title }}</x-slot:header>

    <div class="py-12 px-12">

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-sm text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="max-w-4xl">
            <div class="bg-white">
                <div class="p-6 text-gray-900 space-y-6">

                    <div>
                        <a href="{{ route('trainer-courses.index') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">&larr; Назад к курсам</a>
                    </div>

                    {{-- Информация о курсе --}}
                    <div class="bg-white px-6 py-4 rounded-sm space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Название:</span>
                                <p>{{ $course->title }}</p>
                            </div>
                            @if ($course->direction)
                                <div>
                                    <span class="font-medium text-gray-700">Направление:</span>
                                    <p>{{ $course->direction->name }}</p>
                                </div>
                            @endif
                            @if ($course->duration)
                                <div>
                                    <span class="font-medium text-gray-700">Длительность:</span>
                                    <p>{{ $course->duration }} ч.</p>
                                </div>
                            @endif
                        </div>
                        @if ($course->description)
                            <div class="text-sm">
                                <span class="font-medium text-gray-700">Описание:</span>
                                <p class="mt-1 text-gray-600">{{ $course->description }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Список уроков --}}
                    <div>
                        <h2 class="text-base font-semibold text-gray-800 mb-4">
                            Уроки ({{ $course->lessons->count() }})
                        </h2>

                        @if ($course->lessons->isEmpty())
                            <p class="text-sm text-gray-500 mb-4">В этом курсе пока нет уроков.</p>
                        @else
                            <div class="space-y-3 mb-6">
                                @foreach ($course->lessons as $index => $lesson)
                                    <div class="bg-white px-6 py-4 rounded-sm border border-gray-200">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-medium text-gray-400">{{ $index + 1 }}.</span>
                                                    <span class="font-medium text-gray-900">{{ $lesson->title }}</span>
                                                </div>
                                                @if ($lesson->description)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $lesson->description }}</p>
                                                @endif
                                                @if ($lesson->link)
                                                    <a href="{{ $lesson->link }}" target="_blank" rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mt-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                        </svg>
                                                        Перейти по ссылке
                                                    </a>
                                                @endif
                                            </div>
                                            <form method="POST"
                                                  action="{{ route('trainer-courses.lessons.destroy', [$course, $lesson]) }}"
                                                  onsubmit="return confirm('Удалить урок?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Удалить</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Форма добавления урока --}}
                    <div class="border-t border-gray-300 pt-6">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Добавить урок</h3>

                        <form method="POST" action="{{ route('trainer-courses.lessons.store', $course) }}" class="space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="lesson_title" value="Наименование" />
                                <x-text-input id="lesson_title" name="title" type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('title') }}" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="lesson_description" value="Описание (необязательно)" />
                                <textarea id="lesson_description" name="description" rows="3"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="lesson_link" value="Ссылка (необязательно)" />
                                <x-text-input id="lesson_link" name="link" type="url"
                                    class="mt-1 block w-full"
                                    value="{{ old('link') }}" placeholder="https://..." />
                                <x-input-error :messages="$errors->get('link')" class="mt-1" />
                            </div>

                            <div class="flex items-center gap-4 pt-2">
                                <x-primary-button>Добавить урок</x-primary-button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
