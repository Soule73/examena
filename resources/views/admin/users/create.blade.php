<x-app-layout title="Créer un utilisateur">
    <div class="space-y-6">
        <!-- En-tête -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Créer un utilisateur</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Ajoutez un nouvel utilisateur au système
                        </p>
                    </div>
                    <x-button type="outline" href="{{ route('admin.users.index') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour à la liste
                    </x-button>
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <x-input type="text" id="name" name="name" required value="{{ old('name') }}"
                                placeholder="Nom et prénom" />
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Adresse email <span class="text-red-500">*</span>
                            </label>
                            <x-input type="email" id="email" name="email" required value="{{ old('email') }}"
                                placeholder="email@exemple.com" />
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Rôle -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                            Rôle <span class="text-red-500">*</span>
                        </label>
                        <select id="role" name="role" required
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Sélectionnez un rôle</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                    @if ($role === 'student')
                                        - Peut passer des examens
                                    @elseif($role === 'teacher')
                                        - Peut créer et gérer des examens
                                    @elseif($role->name === 'admin')
                                        - Peut gérer tous les utilisateurs et le système
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Mot de passe -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Mot de passe <span class="text-red-500">*</span>
                            </label>
                            <x-input type="password" id="password" name="password" required
                                placeholder="Minimum 8 caractères" />
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirmation mot de passe -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmer le mot de passe <span class="text-red-500">*</span>
                            </label>
                            <x-input type="password" id="password_confirmation" name="password_confirmation" required
                                placeholder="Retapez le mot de passe" />
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Note informative -->
                    <div class="bg-blue-50 p-4 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Information importante
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>L'utilisateur recevra ses identifiants par email (fonctionnalité à
                                            implémenter)</li>
                                        <li>Il pourra changer son mot de passe lors de sa première connexion</li>
                                        <li>Seuls les administrateurs peuvent gérer les comptes utilisateurs</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <x-button type="outline" href="{{ route('admin.users.index') }}">
                            Annuler
                        </x-button>
                        <x-button type="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Créer l'utilisateur
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
