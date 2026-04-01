<x-app-layout>
    <x-slot:header>Новый поток</x-slot:header>

    <div class="py-12 px-12">
        <div class="max-w-2xl">
            <div class="bg-slate-200">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('trainer-sessions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Назад к потокам</a>
                    </div>

                    <form method="POST" action="{{ route('trainer-sessions.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="course_id" value="Курс" />
                            <select id="course_id" name="course_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="">-- Выберите курс --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-1" />
                        </div>

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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_time" value="Время начала (необязательно)" />
                                <x-text-input id="start_time" name="start_time" type="time"
                                    class="mt-1 block w-full"
                                    value="{{ old('start_time') }}" />
                                <x-input-error :messages="$errors->get('start_time')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="end_time" value="Время окончания (необязательно)" />
                                <x-text-input id="end_time" name="end_time" type="time"
                                    class="mt-1 block w-full"
                                    value="{{ old('end_time') }}" />
                                <x-input-error :messages="$errors->get('end_time')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="location" value="Место проведения (необязательно)" />
                                <x-text-input id="location" name="location" type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('location') }}" placeholder="Кабинет, онлайн и т.д." />
                                <x-input-error :messages="$errors->get('location')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="max_participants" value="Макс. участников (необязательно)" />
                                <x-text-input id="max_participants" name="max_participants" type="number" min="1"
                                    class="mt-1 block w-full"
                                    value="{{ old('max_participants') }}" />
                                <x-input-error :messages="$errors->get('max_participants')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <x-primary-button>Создать поток</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
