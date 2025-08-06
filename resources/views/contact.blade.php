<x-app-layout>
    <div class="bg-gray-100 py-16">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Contactez-nous</h2>
                    <p class="text-center text-sm text-gray-500 mb-8">* champs obligatoire</p>

                    @if (session('success'))
                        <div class="bg-blue-500 text-[#f1f8e9] px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf

                        <!-- Nom -->
                        <div class="mb-4">
                            <label for="nom" class="block text-sm font-medium text-gray-700">Nom *</label>
                            <input type="text" id="nom" name="nom"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required>
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" id="email" name="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required>
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required></textarea>
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
            </div>
        </div>
    </div>
</x-app-layout>
