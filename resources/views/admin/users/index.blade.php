<x-app-layout title="Gestion des utilisateurs">
    <div class="space-y-6">
        <!-- En-tête avec actions -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Gestion des utilisateurs</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Créez et gérez les comptes utilisateurs
                        </p>
                    </div>
                    <x-button type="primary" href="{{ route('admin.users.create') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nouvel utilisateur
                    </x-button>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('success'))
            <x-alert type="success">
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error">
                {{ session('error') }}
            </x-alert>
        @endif

        <!-- Filtres et recherche -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('admin.users.index') }}"
                    class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            Rechercher
                        </label>
                        <x-input type="text" id="search" name="search" placeholder="Nom ou email..."
                            value="{{ request('search') }}" />
                    </div>

                    <div class="w-full md:w-48">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                            Rôle
                        </label>
                        <select id="role" name="role"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Tous les rôles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <x-button type="secondary">
                            Filtrer
                        </x-button>
                        @if (request()->hasAny(['search', 'role']))
                            <x-button type="outline" href="{{ route('admin.users.index') }}">
                                Reset
                            </x-button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @if ($users->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div
                                            class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                                            <span class="text-lg font-medium text-primary-700">
                                                {{ substr($user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-lg font-medium text-gray-900">{{ $user->name }}</p>
                                            @if ($user->id === auth()->id())
                                                <span
                                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Vous
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        <p class="text-xs text-gray-400">
                                            Créé le {{ $user->created_at->format('d/m/Y à H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if ($user->getRoleNames()->first() === 'student') @elseif($user->getRoleNames()->first() === 'teacher')
                                        @else bg-purple-100 text-purple-800 @endif">
                                        {{ ucfirst($user->getRoleNames()->first()) }}
                                    </span>

                                    <div class="flex space-x-2">
                                        <x-button type="outline" size="sm"
                                            href="{{ route('admin.users.edit', $user) }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </x-button>

                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                class="inline"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-button type="danger" size="sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </x-button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <!-- Pagination -->
                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun utilisateur trouvé</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if (request()->hasAny(['search', 'role']))
                            Aucun utilisateur ne correspond à vos critères de recherche.
                        @else
                            Commencez par créer votre premier utilisateur.
                        @endif
                    </p>
                    <div class="mt-6">
                        @if (request()->hasAny(['search', 'role']))
                            <x-button type="outline" href="{{ route('admin.users.index') }}">
                                Voir tous les utilisateurs
                            </x-button>
                        @else
                            <x-button type="primary" href="{{ route('admin.users.create') }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Créer un utilisateur
                            </x-button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
