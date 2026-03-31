<x-app-layout>
    <x-slot:header>Новая заявка на обучение</x-slot:header>

    <div class="py-12 px-12">
        <div class="max-w-2xl">
            <div class="bg-slate-200">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('external-requests.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Назад к заявкам</a>
                    </div>

                    <form method="POST" action="{{ route('external-requests.store') }}" class="space-y-5">
                        @csrf

                        {{-- Название курса --}}
                        <div>
                            <x-input-label for="course_title" value="Название курса" />
                            <x-text-input id="course_title" name="course_title" type="text"
                                class="mt-1 block w-full"
                                value="{{ old('course_title') }}" required />
                            <x-input-error :messages="$errors->get('course_title')" class="mt-1" />
                        </div>

                        {{-- Ссылка на программу --}}
                        <div>
                            <x-input-label for="program_link" value="Ссылка на программу (необязательно)" />
                            <x-text-input id="program_link" name="program_link" type="url"
                                class="mt-1 block w-full"
                                value="{{ old('program_link') }}" placeholder="https://..." />
                            <x-input-error :messages="$errors->get('program_link')" class="mt-1" />
                        </div>

                        {{-- Стоимость --}}
                        <div>
                            <x-input-label for="cost" value="Стоимость (₽)" />
                            <x-text-input id="cost" name="cost" type="number" min="0" step="0.01"
                                class="mt-1 block w-full"
                                value="{{ old('cost') }}" required />
                            <x-input-error :messages="$errors->get('cost')" class="mt-1" />
                        </div>

                        {{-- Даты --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" value="Дата начала" />
                                <x-text-input id="start_date" name="start_date" type="date"
                                    class="mt-1 block w-full"
                                    value="{{ old('start_date') }}" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="end_date" value="Дата окончания" />
                                <x-text-input id="end_date" name="end_date" type="date"
                                    class="mt-1 block w-full"
                                    value="{{ old('end_date') }}" required />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-1" />
                            </div>
                        </div>

                        {{-- Описание --}}
                        <div>
                            <x-input-label for="description" value="Обоснование (необязательно)" />
                            <textarea id="description" name="description" rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                placeholder="Почему этот курс полезен для работы...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <x-primary-button>Отправить заявку</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
