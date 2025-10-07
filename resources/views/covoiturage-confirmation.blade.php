<x-app-layout>
    <main class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header = message de confirmation -->
            <section aria-labelledby="confirmation-title" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="text-center">
                    <h1 id="confirmation-title" class="text-3xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Confirmation de votre participation
                    </h1>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-lg text-gray-700">
                            Bonjour <span class="font-semibold text-green-600">{{ $user->name }}</span> !
                            Veuillez bien relire toutes les informations ci-dessous et confirmer si tout est correct.
                        </p>
                        <p class="text-base text-gray-600 mt-2">
                            Vous vous apprêtez à réserver
                            <span class="font-semibold">{{ session('n_tickets', 1) }} place(s)</span>
                            pour le covoiturage du
                            <span
                                class="font-semibold">{{ \Carbon\Carbon::parse($covoiturage->departure_date)->format('d/m/Y') }}</span>
                            à <span
                                class="font-semibold">{{ \Carbon\Carbon::parse($covoiturage->departure_time)->format('H:i') }}</span>,
                            de <span class="font-semibold">{{ $covoiturage->departure_address }}</span>
                            vers <span class="font-semibold">{{ $covoiturage->arrival_address }}</span>.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Contenu principal (reprise de la modale) -->
            <article aria-labelledby="confirmation-title" class="modal-container bg-white w-11/12 md:max-w-4xl mx-auto rounded-lg shadow-xl z-50 overflow-y-auto max-h-screen">
                <div class="modal-body p-6">
                    <div class="space-y-6">

                        <!-- Info du trajet -->
                        <section aria-labelledby="trip-info-title" class="trip-details-section">
                            <h4 id="trip-info-title" class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-route mr-2 text-green-500"></i>Informations sur le trajet
                            </h4>

                            <!-- Route et dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="trip-details-departure flex flex-col items-center md:items-start">
                                    <h5 class="font-semibold text-gray-700 mb-2">Départ</h5>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-center md:justify-start text-gray-600">
                                            <i class="fas fa-calendar mr-2 text-green-500"></i>
                                            <span>{{ \Carbon\Carbon::parse($covoiturage->departure_date)->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="flex items-center justify-center md:justify-start text-gray-600">
                                            <i class="fas fa-clock mr-2 text-green-500"></i>
                                            <span>{{ \Carbon\Carbon::parse($covoiturage->departure_time)->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="trip-details-arrival flex flex-col items-center md:items-end">
                                    <h5 class="font-semibold text-gray-700 mb-2">Arrivée</h5>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-center md:justify-end text-gray-600">
                                            <i class="fas fa-calendar mr-2 text-green-500"></i>
                                            <span>{{ \Carbon\Carbon::parse($covoiturage->departure_date)->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="flex items-center justify-center md:justify-end text-gray-600">
                                            <i class="fas fa-clock mr-2 text-green-500"></i>
                                            <span>{{ \Carbon\Carbon::parse($covoiturage->arrival_time)->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Adresses -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div
                                    class="address-departure address-departure flex flex-col items-center md:items-start">
                                    <h5 class="font-semibold text-gray-700 mb-2">Adresse de départ</h5>
                                    <div class="flex items-start justify-center md:justify-start text-gray-600">
                                        <i class="fas fa-map-marker-alt mr-2 text-green-500 mt-1"></i>
                                        <span>{{ $covoiturage->departure_address }}</span>
                                    </div>
                                </div>

                                <div class="address-arrival flex flex-col items-center md:items-end">
                                    <h5 class="font-semibold text-gray-700 mb-2">Adresse d'arrivée</h5>
                                    <div class="flex items-start justify-center md:justify-end text-gray-600">
                                        <i class="fas fa-map-marker-alt mr-2 text-green-500 mt-1"></i>
                                        <span>{{ $covoiturage->arrival_address }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Prix et places -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="price-info text-center">
                                    <h5 class="font-semibold text-gray-700 mb-2">Prix par place</h5>
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ $covoiturage->price }} crédits
                                    </div>
                                </div>

                                <div class="seats-info text-center">
                                    <h5 class="font-semibold text-gray-700 mb-2">Places disponibles</h5>
                                    <div class="text-2xl font-bold text-blue-600">
                                        {{ $placesRestantes }}
                                    </div>
                                </div>

                                <div class="duration-info text-center">
                                    <h5 class="font-semibold text-gray-700 mb-2">Durée maximale</h5>
                                    <div class="text-2xl font-bold text-purple-600">
                                        {{ $covoiturage->max_travel_time ? \Carbon\Carbon::parse($covoiturage->max_travel_time)->format('G\hi') : 'Non spécifiée' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Eco ou non -->
                            @if ($covoiturage->eco_travel)
                                <div class="mt-4 text-center">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-leaf mr-2"></i>Trajet écologique
                                    </span>
                                </div>
                            @endif
                        </section>

                        <!-- Info du conducteur -->
                        <section aria-labelledby="driver-info-title" class="driver-details-section">
                            <h4 id="driver-info-title" class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-user mr-2 text-green-500"></i>Informations sur le conducteur
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Photo et nom -->
                                <div class="driver-profile flex items-center justify-center md:justify-start space-x-4">
                                    <x-driver-profile :driver="$conducteur" :avgRating="$notesMoyenne" :totalRatings="$totalRatings" />
                                </div>

                                <!-- Préférences -->
                                <div class="driver-preferences flex flex-col items-center md:items-start">
                                    <h5 class="font-semibold text-gray-700 mb-2">Préférences</h5>
                                    <div class="space-y-2">
                                        @if ($conducteur->pref_smoke)
                                            <div
                                                class="flex items-center justify-center md:justify-start text-gray-600">
                                                <i class="fas fa-smoking mr-2 text-gray-500"></i>
                                                <span>{{ $conducteur->pref_smoke }}</span>
                                            </div>
                                        @endif
                                        @if ($conducteur->pref_pet)
                                            <div
                                                class="flex items-center justify-center md:justify-start text-gray-600">
                                                <i class="fas fa-paw mr-2 text-gray-500"></i>
                                                <span>Animaux {{ $conducteur->pref_pet }}</span>
                                            </div>
                                        @endif
                                        @if ($conducteur->pref_libre)
                                            <div class="flex items-start justify-center md:justify-start text-gray-600">
                                                <i class="fas fa-comment mr-2 text-gray-500 mt-1"></i>
                                                <span>{{ $conducteur->pref_libre }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Info du véhicule -->
                        @if ($voiture)
                            <section aria-labelledby="vehicle-info-title" class="vehicle-details-section">
                                <h4 id="vehicle-info-title" class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                    <i class="fas fa-car mr-2 text-green-500"></i>Informations sur le véhicule
                                </h4>

                                <div class="vehicle-info">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6">
                                        <div class="flex items-center justify-center md:justify-start text-gray-600">
                                            <i class="fas fa-car mr-2 text-blue-500"></i>
                                            <span><strong>Marque :</strong>
                                                {{ $voiture->brand ?? 'Non spécifiée' }}</span>
                                        </div>
                                        <div class="flex items-center justify-center md:justify-start text-gray-600">
                                            <i class="fas fa-cogs mr-2 text-blue-500"></i>
                                            <span><strong>Modèle :</strong>
                                                {{ $voiture->model ?? 'Non spécifié' }}</span>
                                        </div>
                                        <div class="flex items-center justify-center md:justify-start text-gray-600">
                                            <i class="fas fa-palette mr-2 text-blue-500"></i>
                                            <span><strong>Couleur :</strong>
                                                {{ $voiture->color ?? 'Non spécifiée' }}</span>
                                        </div>
                                        <div class="flex items-center justify-center md:justify-start text-gray-600">
                                            <i class="fas fa-leaf mr-2 text-green-500"></i>
                                            <span><strong>Énergie :</strong>
                                                {{ $voiture->energie ?? 'Non spécifiée' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @endif

                        <!-- Avis sur le conducteur -->
                        <section aria-labelledby="reviews-title" class="reviews-section" data-driver-id="{{ $conducteur->user_id }}">
                            <h4 id="reviews-title" class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-comments mr-2 text-green-500"></i>Avis sur le conducteur
                            </h4>
                            <div id="confirmation-reviews-list" class="space-y-4 max-h-64 overflow-y-auto">
                                <div class="text-center text-gray-500 py-8">
                                    <div
                                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto">
                                    </div>
                                    <p class="mt-4">Chargement des avis...</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </article>

            <!-- Footer (btn) -->
            <footer class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('covoiturage') }}"
                        class="px-8 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg transition-colors duration-300 text-center">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                    <button id="confirm-participation-btn"
                        class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors duration-300">
                        <i class="fas fa-check mr-2"></i>Confirmer ma participation
                    </button>
                </div>
            </footer>
        </div>
    </main>

    <!-- Modale de paiement = 2éme confirmation !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!-->
    <div class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center hidden z-50"
        id="paymentModal" role="dialog" aria-modal="true" aria-labelledby="payment-modal-title">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-lg shadow-xl z-50">
            <!-- Header -->
            <div class="modal-header flex justify-between items-center p-6 border-b border-gray-200">
                <h3 id="payment-modal-title" class="text-xl font-bold text-gray-800">Valider la transaction</h3>
                <button class="modal-close text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body p-6">
                <div class="space-y-4">
                    <!-- Crédits actuels -->
                    <div class="bg-blue-50 rounded-lg px-4 py-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Vos crédits actuels :</span>
                            <span class="font-bold text-blue-600">{{ $user->n_credit }} crédits</span>
                        </div>
                    </div>

                    <!-- Détails de la transaction -->
                    <div class="space-y-2 px-4 py-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Prix par place :</span>
                            <span class="font-semibold">{{ $covoiturage->price }} crédits</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Nombre de places :</span>
                            <span class="font-semibold" id="payment-seats">{{ session('n_tickets', 1) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between items-center text-lg">
                            <span class="text-gray-700">Coût total :</span>
                            <span class="font-bold text-red-600"
                                id="payment-total">{{ $covoiturage->price * session('n_tickets', 1) }}
                                crédits</span>
                        </div>
                    </div>

                    <!-- Crédits restants -->
                    <div class="bg-green-50 rounded-lg px-4 py-2">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Crédits restants :</span>
                            <span class="font-bold text-green-600"
                                id="payment-remaining">{{ $user->n_credit - $covoiturage->price * session('n_tickets', 1) }}
                                crédits</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer flex justify-end space-x-4 p-6 border-t border-gray-200">
                <button
                    class="modal-close px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded transition-colors duration-300">
                    Annuler
                </button>
                <button id="validate-transaction-btn"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded transition-colors duration-300">
                    Valider la transaction
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmBtn = document.getElementById('confirm-participation-btn');
            const paymentModal = document.getElementById('paymentModal');
            const closeButtons = paymentModal.querySelectorAll('.modal-close');
            const validateBtn = document.getElementById('validate-transaction-btn');

            // Ouvrir la modale de paiement
            confirmBtn.addEventListener('click', function() {
                paymentModal.classList.remove('hidden');
            });

            // Fermer la modale
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    paymentModal.classList.add('hidden');
                });
            });

            // Fermer si on clique hors de la modale
            paymentModal.addEventListener('click', function(event) {
                if (event.target === paymentModal || event.target.classList.contains('modal-overlay')) {
                    paymentModal.classList.add('hidden');
                }
            });

            // Validation de la transaction
            if (validateBtn) {
                validateBtn.addEventListener('click', function() {
                    validateBtn.disabled = true;
                    validateBtn.textContent = 'Traitement en cours...';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content');
                    const tripId = {{ $covoiturage->covoit_id }};

                    fetch(`/covoiturage/${tripId}/participate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                window.location.href = '{{ route('dashboard') }}';
                            } else {
                                alert('Erreur: ' + data.message);
                                validateBtn.disabled = false;
                                validateBtn.textContent = 'Valider la transaction';
                            }
                        })
                        .catch(error => {
                            console.error('Erreur Fetch:', error);
                            alert('Une erreur de communication est survenue.');
                            validateBtn.disabled = false;
                            validateBtn.textContent = 'Valider la transaction';
                        });
                });
            }

            // Charger les avis
            const reviewsSection = document.querySelector('.reviews-section');
            const driverId = reviewsSection.dataset.driverId;
            const reviewsContainer = document.getElementById('confirmation-reviews-list');
            if (window.fetchAndDisplayReviews) {
                window.fetchAndDisplayReviews(driverId, reviewsContainer);
            }
        });
    </script>
</x-app-layout>
