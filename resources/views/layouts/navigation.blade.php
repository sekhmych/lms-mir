@php
    $sidebarLinks = [];

    $sidebarLinks[] = ['href' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'label' => 'Главная', 'icon' => 'home'];

    if (auth()->user()->hasRole('employee')) {
        $sidebarLinks[] = ['href' => route('my-courses.index'), 'active' => request()->routeIs('my-courses.*'), 'label' => 'Мои курсы', 'icon' => 'book-open'];
        $sidebarLinks[] = ['href' => route('courses.index'), 'active' => request()->routeIs('courses.*'), 'label' => 'Курсы', 'icon' => 'academic-cap'];
        $sidebarLinks[] = ['href' => route('external-requests.index'), 'active' => request()->routeIs('external-requests.*'), 'label' => 'Заявки на обучение', 'icon' => 'document-text'];
    }

    if (auth()->user()->hasRole('trainer')) {
        $sidebarLinks[] = ['href' => route('trainer-courses.index'), 'active' => request()->routeIs('trainer-courses.*'), 'label' => 'Мои курсы', 'icon' => 'book-open'];
        $sidebarLinks[] = ['href' => route('trainer-sessions.index'), 'active' => request()->routeIs('trainer-sessions.*'), 'label' => 'Мои потоки', 'icon' => 'user-group'];
    }

    if (auth()->user()->hasRole('hr')) {
        $sidebarLinks[] = ['href' => route('hr.index'), 'active' => request()->routeIs('hr.index'), 'label' => 'HR-панель', 'icon' => 'chart-bar'];
        $sidebarLinks[] = ['href' => route('hr.external-requests'), 'active' => request()->routeIs('hr.external-requests*'), 'label' => 'Заявки', 'icon' => 'document-text'];
        $sidebarLinks[] = ['href' => route('hr.sessions'), 'active' => request()->routeIs('hr.sessions*'), 'label' => 'Потоки', 'icon' => 'user-group'];
    }

    if (auth()->user()->hasRole('admin')) {
        $sidebarLinks[] = ['href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*'), 'label' => 'Пользователи', 'icon' => 'users'];
    }
@endphp

{{-- Mobile top bar --}}
<div x-data="{ mobileOpen: false }" class="sm:hidden">
    <div class="bg-gray-900 border-b border-gray-700 flex items-center justify-between px-4 h-14">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('logo2.png') }}" alt="{{ config('app.name') }}" class="h-8 w-auto" />
        </a>
        <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-md text-gray-500 hover:bg-gray-100">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <div x-show="mobileOpen" x-cloak @click.outside="mobileOpen = false" class="bg-white border-b border-gray-200 px-4 pb-3 pt-2 space-y-1">
        @foreach ($sidebarLinks as $link)
            <a href="{{ $link['href'] }}"
               class="block px-3 py-2 rounded-md text-sm {{ $link['active'] ? 'text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}"
               @if($link['active']) style="background-color: rgba(129, 208, 245, 0.5);" @endif>
                {{ $link['label'] }}
            </a>
        @endforeach
        <div class="border-t border-gray-200 mt-2 pt-2">
            <div class="px-3 py-1 text-xs text-gray-400">{{ Auth::user()->name }}</div>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-100">Профиль</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-100">Выход</button>
            </form>
        </div>
    </div>
</div>

{{-- Desktop sidebar --}}
<aside class="hidden sm:flex sm:flex-col w-64 shrink-0 bg-white border-r border-gray-200 min-h-screen">
    {{-- Logo --}}
    <div class="h-16 flex items-center px-6 border-b border-gray-100 bg-gray-900">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('logo2.png') }}" alt="{{ config('app.name') }}" class="h-10 w-auto" />
        </a>
    </div>

    {{-- Navigation links --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @foreach ($sidebarLinks as $link)
            <a href="{{ $link['href'] }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-colors
                      {{ $link['active']
                          ? 'text-gray-900'
                          : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
               @if($link['active']) style="background-color: rgba(129, 208, 245, 0.5);" @endif>
                @switch($link['icon'])
                    @case('home')
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
                        @break
                    @case('book-open')
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        @break
                    @case('academic-cap')
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 12.75c0 2.278-.79 4.368-2.102 6.01l-.07.087M12 14l-6.16-3.422A12.083 12.083 0 003 12.75c0 2.278.79 4.368 2.102 6.01l.07.087M12 14v7.5"/></svg>
                        @break
                    @case('document-text')
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        @break
                    @case('user-group')
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @break
                    @case('chart-bar')
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        @break
                    @case('users')
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        @break
                @endswitch
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- User section --}}
    <div class="border-t border-gray-200 px-3 py-4" x-data="{ userMenu: false }">
        <button @click="userMenu = !userMenu" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-md text-sm text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="flex-1 text-left truncate font-medium">{{ Auth::user()->name }}</span>
            <svg class="w-4 h-4 shrink-0 transition-transform" :class="{ 'rotate-180': userMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
        </button>
        <div x-show="userMenu" x-cloak x-transition class="mt-1 space-y-1">
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-100 pl-11">Профиль</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-100 pl-11">Выход</button>
            </form>
        </div>
    </div>
</aside>
