<x-app-layout>
    <x-slot:header>Пользователи</x-slot:header>

    <div class="py-12 px-12">
        <div class="bg-slate-200">
            <div class="p-6 text-gray-900 space-y-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Все пользователи</h1>
                        <p class="text-sm text-gray-500">Управление учётными записями и ролями.</p>
                    </div>
                    <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                        Создать пользователя
                    </a>
                </div>

                @if (session('success'))
                    <div class="px-4 py-3 bg-green-100 text-green-800 rounded-sm text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white border border-slate-300 rounded-sm overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-100 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-3 font-medium">Имя</th>
                                <th class="px-4 py-3 font-medium">Email</th>
                                <th class="px-4 py-3 font-medium">Должность</th>
                                <th class="px-4 py-3 font-medium">Роль</th>
                                <th class="px-4 py-3 font-medium">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->position ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->roles->pluck('name')->implode(', ') ?: '—' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-800 underline hover:text-black">
                                                Редактировать
                                            </a>

                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Удалить пользователя {{ $user->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 underline hover:text-red-700">
                                                    Удалить
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>