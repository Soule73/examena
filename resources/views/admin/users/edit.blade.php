<x-app-layout title="Modifier l'utilisateur">
    <div class="space-y-6">
        <!-- En-tête -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Modifier l'utilisateur</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Modifiez les informations de {{ $user->name }}
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
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <x-input type="text" id="name" name="name" required
                                value="{{ old('name', $user->name) }}" placeholder="Nom et prénom" />
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Adresse email <span class="text-red-500">*</span>
                            </label>
                            <x-input type="email" id="email" name="email" required
                                value="{{ old('email', $user->email) }}" placeholder="email@exemple.com" />
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
                                <option value="{{ $role }}"
                                    {{ old('role', $userRole) === $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                    @if ($role === 'student')
                                        - Peut passer des examens
                                    @elseif($role === 'teacher')
                                        - Peut créer et gérer des examens
                                    @elseif($role === 'admin')
                                        - Peut gérer tous les utilisateurs et le système
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Section mot de passe -->
                    <div class="space-y-4">
                        <div class="bg-yellow-50 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.82-.833-2.59 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Modification du mot de passe
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Laissez les champs vides si vous ne souhaitez pas modifier le mot de passe
                                            actuel.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nouveau mot de passe -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nouveau mot de passe
                                </label>
                                <x-input type="password" id="password" name="password"
                                    placeholder="Minimum 8 caractères" />
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirmation mot de passe -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirmer le nouveau mot de passe
                                </label>
                                <x-input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Retapez le nouveau mot de passe" />
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Informations sur l'utilisateur -->
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Informations sur le compte</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-500">Créé le</dt>
                                <dd class="text-gray-900">{{ $user->created_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Dernière modification</dt>
                                <dd class="text-gray-900">{{ $user->updated_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                            @if ($user->id === auth()->id())
                                <div class="md:col-span-2">
                                    <div class="flex items-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.82-.833-2.59 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                </path>
                                            </svg>
                                            C'est votre propre compte
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <x-button type="outline" href="{{ route('admin.users.index') }}">
                            Annuler
                        </x-button>
                        <x-button type="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mettre à jour
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
