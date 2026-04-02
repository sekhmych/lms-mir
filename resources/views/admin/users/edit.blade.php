<x-app-layout>
    <x-slot:header>Редактирование пользователя</x-slot:header>

    <div class="py-12 px-12">
        <div class="max-w-3xl bg-white">
            <div class="p-6 text-gray-900">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white border border-slate-300 rounded-sm p-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">ФИО</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 rounded-sm" required>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 rounded-sm" required>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Должность</label>
                        <input id="position" name="position" type="text" value="{{ old('position', $user->position) }}" class="w-full border-gray-300 rounded-sm">
                        <x-input-error :messages="$errors->get('position')" class="mt-2" />
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Роль</label>
                        <select id="role" name="role" class="w-full border-gray-300 rounded-sm" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role', $user->roles->first()?->name) === $role->name)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Новый пароль</label>
                        <input id="password" name="password" type="text" class="w-full border-gray-300 rounded-sm">
                        <p class="mt-1 text-xs text-gray-500">Оставьте пустым, чтобы не менять пароль.</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                            Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>