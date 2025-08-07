<x-app-layout>
    <div class="covoiturage-container max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Rechercher un covoiturage</h1>

        <section class="search-section bg-white rounded-lg shadow-md p-8 mb-12">
            <!-- Le formulaire de recherche sera ici -->
        </section>

        @if (isset($covoiturages) && count($covoiturages) > 0)
            <div class="results-title flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Trajets disponibles</h2>
                <p class="text-gray-600">{{ count($covoiturages) }} résultat(s) trouvé(s)</p>
            </div>

            <section class="covoiturage-list grid gap-6">
                @foreach ($covoiturages as $covoiturage)
                    <div
                        class="covoiturage-card bg-white rounded-lg shadow-lg overflow-hidden flex flex-col md:flex-row transition-transform duration-300 hover:transform hover:-translate-y-1 hover:shadow-xl">
                        <div
                            class="covoiturage-driver w-full md:w-1/4 p-6 bg-gray-50 border-b md:border-b-0 md:border-r border-gray-200 flex flex-col items-center justify-center text-center">
                            <div
                                class="driver-photo w-24 h-24 rounded-full border-4 border-green-400 shadow-md mb-4 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user text-4xl text-gray-500"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $covoiturage['pseudo_chauffeur'] }}</h3>
                            <div class="driver-rating flex items-center gap-2 mt-1">
                                <span
                                    class="rating-value font-bold text-yellow-500">{{ $covoiturage['note_chauffeur'] }}</span>
                                <span class="rating-stars text-yellow-500">
                                    
                                </span>
                            </div>
                        </div>

                        <div class="covoiturage-details w-full md:w-1/2 p-6 flex flex-col justify-center">
                            <div class="trip-info-container">
                                <div
                                    class="trip-route flex items-center justify-center text-2xl font-bold text-gray-800 mb-4">
                                    <span class="from">{{ $covoiturage['lieu_depart'] }}</span>
                                    <span class="route-arrow mx-4 text-gray-400">→</span>
                                    <span class="to">{{ $covoiturage['lieu_arrivee'] }}</span>
                                </div>
                                <div class="trip-date text-center text-lg font-medium text-gray-700 mb-4">
                                    <i class="fas fa-calendar-alt mr-2 text-green-500"></i>
                                    {{ date('d/m/Y', strtotime($covoiturage['date_depart'])) }}
                                </div>
                                <div class="trip-time flex justify-between text-gray-600">
                                    <span class="departure-time">
                                        <i class="fas fa-clock mr-2 text-green-500"></i>
                                        Départ: {{ substr($covoiturage['heure_depart'], 0, 5) }}
                                    </span>
                                    <span class="arrival-time">
                                        <i class="fas fa-clock mr-2 text-green-500"></i>
                                        Arrivée: {{ substr($covoiturage['heure_arrivee'], 0, 5) }}
                                    </span>
                                </div>
                            </div>
                            @if ($covoiturage['ecologique'])
                                <div
                                    class="trip-eco-badge self-center mt-4 px-4 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                    <i class="fas fa-leaf mr-2"></i>Voyage écologique
                                </div>
                            @endif
                        </div>

                        <div
                            class="covoiturage-booking w-full md:w-1/4 p-6 bg-gray-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col items-center justify-center">
                            <div class="trip-seats text-gray-600 mb-4">
                                <i class="fas fa-user-friends mr-2"></i>
                                {{ $covoiturage['places_restantes'] }}
                                {{ $covoiturage['places_restantes'] > 1 ? 'places disponibles' : 'place disponible' }}
                            </div>
                            <div class="trip-price text-center mb-4">
                                <span class="price-value text-3xl font-bold text-green-500">{{ $covoiturage['prix'] }}
                                    crédits</span>
                                <span class="price-per-person text-sm text-gray-500">
                                    <br>par personne</span>
                            </div>
                            <div class="booking-buttons flex flex-col gap-2 w-full">
                                <a href="#"
                                    class="btn-details bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center transition-colors duration-300"
                                    data-id="{{ $covoiturage['id'] }}">
                                    Détails
                                </a>
                                <a href="#"
                                    class="btn-participate bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-center transition-colors duration-300">
                                    Participer
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @else
            <div class="no-results bg-white rounded-lg shadow-md p-8 text-center">
                <p class="text-xl text-gray-600">Aucun covoiturage disponible pour le moment.</p>
            </div>
        @endif
    </div>

    <!-- Modale -->
    <div class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center hidden" id="tripDetailsModal">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-3xl mx-auto rounded shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-6">
                <div class="modal-header flex justify-between items-center pb-3">
                    <p class="text-2xl font-bold">Détails du covoiturage</p>
                    <div class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                            height="18" viewBox="0 0 18 18">
                            <path
                                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                            </path>
                        </svg>
                    </div>
                </div>

                <div class="modal-body">
                    <!-- Contenu chargé par JS -->
                </div>

                <div class="modal-footer flex justify-end pt-2">
                    <button
                        class="modal-close-btn px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">Fermer</button>
                    <a href="#"
                        class="btn-participate-modal px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">Participer</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Logique de la modale
                const modal = document.getElementById('tripDetailsModal');
                const closeButtons = modal.querySelectorAll('.modal-close, .modal-close-btn');
                const detailsButtons = document.querySelectorAll('.btn-details');

                detailsButtons.forEach(button => {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        const tripId = this.getAttribute('data-id');
                        // TODO: appel fetch pour obtenir les détails du voyage et remplir la modale.
                        modal.classList.remove('hidden');
                    });
                });

                closeButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        modal.classList.add('hidden');
                    });
                });

                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        modal.classList.add('hidden');
                    }
                });

                // Logique pour les étoiles
                function generateStars(rating) {
                    let starsHtml = '';
                    const fullStars = Math.floor(rating);
                    const hasHalfStar = rating - fullStars >= 0.5;

                    for (let i = 0; i < fullStars; i++) {
                        starsHtml += '<i class="fas fa-star"></i>';
                    }

                    if (hasHalfStar) {
                        starsHtml += '<i class="fas fa-star-half-alt"></i>';
                    }

                    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
                    for (let i = 0; i < emptyStars; i++) {
                        starsHtml += '<i class="far fa-star"></i>';
                    }

                    return starsHtml;
                }

                document.querySelectorAll('.rating-stars').forEach(starContainer => {
                    const rating = parseFloat(starContainer.previousElementSibling.textContent);
                    if (!isNaN(rating)) {
                        starContainer.innerHTML = generateStars(rating);
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
