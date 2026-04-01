<x-app-layout>
    <x-slot:header>Заявки на обучение</x-slot:header>

    <div class="py-12 px-12">
        <div class="">
            <div class="bg-slate-200">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-sm text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <p class="text-sm text-gray-600">Ваши заявки на оплату внешнего обучения</p>
                        <a href="{{ route('external-requests.create') }}"
                           class="px-4 py-2 bg-gray-800 text-white text-sm rounded-sm hover:bg-gray-700 transition-colors">
                            + Новая заявка
                        </a>
                    </div>

                    @if ($requests->isEmpty())
                        <p class="text-gray-500 text-sm">У вас ещё нет заявок.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($requests as $request)
                                @php
                                    $statusLabels = [
                                        'draft'     => ['label' => 'Черновик', 'class' => 'bg-gray-100 text-gray-600'],
                                        'pending'   => ['label' => 'На согласовании','class' => 'bg-yellow-100 text-yellow-800'],
                                        'approved'  => ['label' => 'Одобрена', 'class' => 'bg-green-100 text-green-800'],
                                        'rejected'  => ['label' => 'Отклонена', 'class' => 'bg-red-100 text-red-800'],
                                        'completed' => ['label' => 'Завершена',      'class' => 'bg-blue-100 text-blue-800'],
                                    ];
                                    $status = $statusLabels[$request->status] ?? ['label' => $request->status, 'class' => 'bg-gray-100 text-gray-600'];
                                @endphp
                                <div class="bg-gray-50 px-6 py-4 rounded-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-gray-900 truncate">{{ $request->course_title }}</div>
                                        <div class="text-sm text-gray-500 mt-1 space-x-4">
                                            <span>{{ number_format($request->cost, 2, ',', ' ') }} ₽</span>
                                            <span>{{ \Carbon\Carbon::parse($request->start_date)->format('d.m.Y') }} – {{ \Carbon\Carbon::parse($request->end_date)->format('d.m.Y') }}</span>
                                        </div>
                                    </div>
                                    <span class="self-start sm:self-center text-xs font-medium px-2.5 py-1 rounded-full whitespace-nowrap {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $requests->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
