<x-guest-layout>
    <!-- TODO: Remettre "Remember me" et "Forgot your password?"!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!-->

    <div class="bg-gray-100 py-16">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Connexion</h2>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="username"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Honeypot => voir Honeypot.php (app/Rules) -->
                        <div class="hidden">
                            <label for="raison_sociale">Raison Sociale</label>
                            <input type="text" id="raison_sociale" name="raison_sociale" tabindex="-1"
                                autocomplete="off">
                        </div>

                        <!-- Btn -->
                        <div class="text-center">
                            <button type="submit"
                                class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-300">
                                Se connecter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
