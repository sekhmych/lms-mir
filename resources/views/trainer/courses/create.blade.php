<x-app-layout>
    <x-slot:header>Новый курс</x-slot:header>

    <div class="py-12 px-12">
        <div class="max-w-2xl">
            <div class="bg-white">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('trainer-courses.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Назад к курсам</a>
                    </div>

                    <form method="POST" action="{{ route('trainer-courses.store') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="title" value="Название курса" />
                            <x-text-input id="title" name="title" type="text"
                                class="mt-1 block w-full"
                                value="{{ old('title') }}" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Описание (необязательно)" />
                            <textarea id="description" name="description" rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="direction_id" value="Направление (необязательно)" />
                                <select id="direction_id" name="direction_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Не выбрано --</option>
                                    @foreach ($directions as $direction)
                                        <option value="{{ $direction->id }}" @selected(old('direction_id') == $direction->id)>
                                            {{ $direction->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('direction_id')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="duration" value="Длительность в часах (необязательно)" />
                                <x-text-input id="duration" name="duration" type="number" min="1"
                                    class="mt-1 block w-full"
                                    value="{{ old('duration') }}" />
                                <x-input-error :messages="$errors->get('duration')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <x-primary-button>Создать курс</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
