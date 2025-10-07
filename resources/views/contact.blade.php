<x-app-layout>
    <main class="bg-gray-100 py-16">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <section aria-labelledby="contact-title" class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-8">
                    <h2 id="contact-title" class="text-2xl font-bold text-center text-gray-800 mb-2">Contactez-nous</h2>
                    <p class="text-center text-sm text-gray-500 mb-8">* champs obligatoire</p>

                    @if (session('success'))
                        <div class="bg-blue-500 text-[#f1f8e9] px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form id="contactForm" action="{{ route('contact.store') }}" method="POST">
                        @csrf

                        <!--Honeypot-->
                        <div class="hidden">
                            <label for="raison_sociale">Raison Sociale</label>
                            <input type="text" id="raison_sociale" name="raison_sociale" tabindex="-1"
                                autocomplete="off">
                        </div>

                        <!-- Nom -->
                        <div class="mb-4">
                            <label for="nom" class="block text-sm font-medium text-gray-700">Nom *</label>
                            <input type="text" id="nom" name="nom"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required oninput="validateNameInput(this)">
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="email" name="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required oninput="validateEmailInput(this)">
                            <small id="email-error" class="text-red-600 mt-1 hidden">Adresse email invalide.</small>
                        </div>

                        <!-- Sujet -->
                        <div class="mb-4">
                            <label for="sujet" class="block text-sm font-medium text-gray-700">Sujet *</label>
                            <select id="sujet" name="sujet"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 accent-green-600"
                                required>
                                <option value="" disabled selected>Sélectionnez un sujet</option>
                                <option value="Support technique">Support technique</option>
                                <option value="Problème lié à une réservation">Problème lié à une réservation</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-700">Message *</label>
                            <textarea id="message" name="message" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required
                                oninput="validateMessageInput(this)"></textarea>
                        </div>

                        <!-- Btn -->
                        <div class="text-center">
                            <button type="submit"
                                class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-300">
                                Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>
</x-app-layout>

<script>
    function validateNameInput(input) {
        // Autorise les lettres, les lettres avec accent, les espaces et les tirets.
        input.value = input.value.replace(/[^a-zA-Zà-ÿÀ-Ÿ\s-]/g, '');
    }

    function validateMessageInput(textarea) {
        const value = textarea.value;
        if (value.length === 1) {
            // Pour 1er caractère, autorise lettres, chiffres, «, ¨, (.
            if (!/^[a-zA-Z0-9«"¨\(]$/.test(value)) {
                textarea.value = '';
            }
        }
    }

    function validateEmailInput(input) {
        const value = input.value;
        if (value.length === 1) {
            // Le 1er caractère ne peut pas être un caractère spécial.
            if (/[^a-zA-Z0-9]/.test(value)) {
                input.value = '';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contactForm');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');

        contactForm.addEventListener('submit', function(e) {
            const emailRegex = /^\S+@\S+\.\S+$/;
            if (!emailRegex.test(emailInput.value)) {
                e.preventDefault();
                emailError.classList.remove('hidden');
            } else {
                emailError.classList.add('hidden');
            }
        });
    });
</script>
