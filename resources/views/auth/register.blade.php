<x-guest-layout>
    <div class="bg-gray-100 py-16">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-center text-gray-800 mb-3">Inscription</h2>

                    <div class="bg-blue-500 text-gray-100 p-4 rounded-md text-center mb-12">
                        <p class="font-extrabold">20 crédits vous seront offerts à votre première inscription !!!</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Pseudo est en fait name dans la base de donnée -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Pseudo *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required autofocus autocomplete="name">
                            <div id="name-feedback" class="mt-2 text-sm p-2 rounded-md bg-[#3b82f6] text-white">
                                Le pseudo ne doit pas dépasser 18 caractères.
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required autocomplete="username">
                            <div id="email-feedback" class="mt-2 text-sm p-2 rounded-md" style="display: none;"></div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe *</label>
                            <input type="password" id="password" name="password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required autocomplete="new-password">
                            <div id="password-feedback" class="mt-2 text-sm space-y-1">
                                <p id="pass-length" class="text-red-600 font-bold">✗ Au moins 8 caractères</p>
                                <p id="pass-uppercase" class="text-red-600 font-bold">✗ Au moins une majuscule</p>
                                <p id="pass-lowercase" class="text-red-600 font-bold">✗ Au moins une minuscule</p>
                                <p id="pass-number" class="text-red-600 font-bold">✗ Au moins un chiffre</p>
                                <p id="pass-symbol" class="text-red-600 font-bold">✗ Au moins un symbole</p>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer
                                le mot de passe *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required autocomplete="new-password">
                            <div id="password-confirm-feedback" class="mt-2 text-sm p-2 rounded-md"
                                style="display: none;"></div>
                        </div>

                        <!-- Honeypot => voir Honeypot.php (app/Rules) -->
                        <div class="hidden">
                            <label for="raison_sociale">Raison Sociale</label>
                            <input type="text" id="raison_sociale" name="raison_sociale" tabindex="-1"
                                autocomplete="off">
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                href="{{ route('login') }}">
                                Déjà enregistré ?
                            </a>
                        </div>

                        <!-- Btn -->
                        <div class="text-center mt-6">
                            <button type="submit"
                                class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-300">
                                S'enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const nameFeedback = document.getElementById('name-feedback');

            const emailInput = document.getElementById('email');
            const emailFeedback = document.getElementById('email-feedback');

            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const passwordConfirmFeedback = document.getElementById('password-confirm-feedback');

            const passLength = document.getElementById('pass-length');
            const passUppercase = document.getElementById('pass-uppercase');
            const passLowercase = document.getElementById('pass-lowercase');
            const passNumber = document.getElementById('pass-number');
            const passSymbol = document.getElementById('pass-symbol');

            const successColor = '#3b82f6';
            const errorColor = '#dc2626';

            function setFeedback(element, message, isValid) {
                element.style.display = 'block';
                element.innerHTML = message;
                if (isValid) {
                    element.style.backgroundColor = successColor;
                    element.style.color = 'white';
                    element.style.fontWeight = 'normal';
                } else {
                    element.style.backgroundColor = 'transparent';
                    element.style.color = errorColor;
                    element.style.fontWeight = 'bold';
                }
            }

            function setPasswordRequirementFeedback(element, isValid) {
                if (isValid) {
                    element.innerHTML = `✓ ${element.innerText.substring(2)}`;
                    element.style.color = 'white';
                    element.style.backgroundColor = successColor;
                    element.classList.remove('text-red-600', 'font-bold');
                    element.classList.add('text-white', 'p-1', 'rounded-md');
                } else {
                    element.innerHTML = `✗ ${element.innerText.substring(2)}`;
                    element.style.color = errorColor;
                    element.style.backgroundColor = 'transparent';
                    element.classList.add('text-red-600', 'font-bold');
                    element.classList.remove('text-white', 'p-1', 'rounded-md');
                }
            }

            nameInput.addEventListener('input', function() {
                if (nameInput.value.length > 18) {
                    nameFeedback.style.backgroundColor = 'transparent';
                    nameFeedback.style.color = errorColor;
                    nameFeedback.style.fontWeight = 'bold';
                    nameFeedback.innerHTML = '✗ Le pseudo ne doit pas dépasser 18 caractères.';
                } else {
                    nameFeedback.style.backgroundColor = successColor;
                    nameFeedback.style.color = 'white';
                    nameFeedback.style.fontWeight = 'normal';
                    nameFeedback.innerHTML = 'Le pseudo ne doit pas dépasser 18 caractères.';
                }
            });

            emailInput.addEventListener('input', function() {
                const emailRegex =
                    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (emailRegex.test(emailInput.value)) {
                    setFeedback(emailFeedback, '✓ Email valide', true);
                } else {
                    setFeedback(emailFeedback, '✗ Email invalide', false);
                }
            });

            passwordInput.addEventListener('input', function() {
                const value = passwordInput.value;
                setPasswordRequirementFeedback(passLength, value.length >= 8);
                setPasswordRequirementFeedback(passUppercase, /[A-Z]/.test(value));
                setPasswordRequirementFeedback(passLowercase, /[a-z]/.test(value));
                setPasswordRequirementFeedback(passNumber, /[0-9]/.test(value));
                setPasswordRequirementFeedback(passSymbol, /[^A-Za-z0-9]/.test(value));
                validatePasswordConfirmation();
            });

            passwordConfirmInput.addEventListener('input', validatePasswordConfirmation);

            function validatePasswordConfirmation() {
                if (passwordConfirmInput.value === '' && passwordInput.value === '') {
                    passwordConfirmFeedback.style.display = 'none';
                    return;
                }

                if (passwordConfirmInput.value === passwordInput.value) {
                    setFeedback(passwordConfirmFeedback, '✓ Les mots de passe correspondent', true);
                } else {
                    setFeedback(passwordConfirmFeedback, '✗ Les mots de passe ne correspondent pas', false);
                }
            }
        });
    </script>
</x-guest-layout>
