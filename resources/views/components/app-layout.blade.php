@props(['title' => null])

<x-layout :title="$title">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-800">Examena</span>
                    </a>

                    <!-- Navigation principale -->
                    <div class="hidden md:flex space-x-8 ml-10">
                        @can('view-exams')
                            <a href="#"
                                class="text-gray-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">Examens</a>
                        @endcan
                        @can('create-exams')
                            <a href="#" class="text-gray-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">Mes
                                Examens</a>
                        @endcan
                        @role('admin')
                            <a href="{{ route('admin.users.index') }}"
                                class="text-gray-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">Utilisateurs</a>
                        @endrole
                    </div>
                </div>

                <!-- Menu utilisateur -->
                <div class="flex items-center space-x-4">
                    <!-- Nom utilisateur et rôle -->
                    <div class="hidden md:flex flex-col text-right">
                        <span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
                        <span
                            class="text-xs text-gray-500 capitalize">{{ auth()->user()->getRoleNames()->first() }}</span>
                    </div>

                    <!-- Menu dropdown -->
                    <div class="relative">
                        <button type="button"
                            class="flex items-center text-sm rounded-full bg-gray-100 p-2 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            onclick="document.getElementById('user-menu').classList.toggle('hidden')">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </button>

                        <div id="user-menu"
                            class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages flash -->
            @if (session('success'))
                <x-alert type="success" class="mb-6">
                    {{ session('success') }}
                </x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-6">
                    {{ session('error') }}
                </x-alert>
            @endif

            {{ $slot }}
        </div>
    </main>
</x-layout>
