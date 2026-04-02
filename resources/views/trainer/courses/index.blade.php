<x-app-layout>
    <x-slot:header>Мои курсы</x-slot:header>

    <div class="py-12 px-12">
        <div class="">
            <div class="bg-white">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-sm text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <p class="text-sm text-gray-600">Управляйте своими внутренними курсами</p>
                        <a href="{{ route('trainer-courses.create') }}"
                           class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                            + Новый курс
                        </a>
                    </div>

                    @if ($courses->isEmpty())
                        <p class="text-gray-500 text-sm">У вас ещё нет курсов.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($courses as $course)
                                <a href="{{ route('trainer-courses.show', $course) }}"
                                   class="block bg-white px-6 py-4 rounded-sm hover:bg-gray-100 transition-colors border border-gray-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900">{{ $course->title }}</div>
                                            <div class="text-sm text-gray-500 mt-1 space-x-3">
                                                @if ($course->direction)
                                                    <span>{{ $course->direction->name }}</span>
                                                @endif
                                                <span>{{ $course->lessons_count }} {{ trans_choice('урок|урока|уроков', $course->lessons_count) }}</span>
                                                @if ($course->duration)
                                                    <span>{{ $course->duration }} ч.</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $courses->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
