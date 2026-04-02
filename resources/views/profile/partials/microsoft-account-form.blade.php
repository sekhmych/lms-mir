<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Учётная запись Microsoft 365
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Привяжите свою учётную запись Microsoft 365 для доступа к дополнительным возможностям.
        </p>
    </header>

    <div class="mt-6">
        @if ($microsoftConnected)
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 text-sm text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Учётная запись Microsoft 365 привязана ({{ $microsoftEmail }})</span>
                </div>
            </div>

            <form method="post" action="{{ route('profile.microsoft.disconnect') }}" class="mt-4">
                @csrf
                <x-danger-button type="submit">Отвязать Microsoft 365</x-danger-button>
            </form>

            @if (session('status') === 'microsoft-disconnected')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="mt-2 text-sm text-gray-600"
                >Учётная запись Microsoft 365 отвязана.</p>
            @endif
        @else
            <a href="{{ route('msgraph.connect') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Привязать Microsoft 365
            </a>

            @if (session('status') === 'microsoft-connected')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="mt-2 text-sm text-green-600"
                >Учётная запись Microsoft 365 успешно привязана.</p>
            @endif
        @endif
    </div>
</section>
