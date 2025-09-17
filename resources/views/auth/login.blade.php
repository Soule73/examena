<x-auth-layout title="Connexion">
    <div class="mb-6 text-center">
        <h2 class="text-3xl font-bold text-gray-900">Connexion</h2>
        <p class="mt-2 text-sm text-gray-600">
            Connectez-vous à votre compte pour accéder à vos examens
        </p>
    </div>

    @if (session('status'))
        <x-alert type="success" class="mb-6">
            {{ session('status') }}
        </x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <x-input type="email" name="email" label="Adresse e-mail" placeholder="votre@email.com" required
            :error="$errors->first('email')" />

        <x-input type="password" name="password" label="Mot de passe" placeholder="••••••••" required
            :error="$errors->first('password')" />

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox"
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-900">
                    Se souvenir de moi
                </label>
            </div>
        </div>

        <div>
            <x-button type="primary" class="w-full" formType="submit">
                Se connecter
            </x-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">
            Pas de compte ou mot de passe oublié ? Contactez votre administrateur.
        </p>
    </div>
</x-auth-layout>
