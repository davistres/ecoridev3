<!-- Modale au clic sur le btn "Détails" des covoiturage-card -->
<div class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center hidden z-50" id="tripDetailsModal">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div
        class="modal-container bg-white w-11/12 md:max-w-4xl mx-auto rounded-lg shadow-xl z-50 overflow-y-auto max-h-screen">

        <!-- Header -->
        <div class="modal-header flex justify-between items-center p-6 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">Détails du covoiturage</h3>
            <button class="modal-close text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Main -->
        <div class="modal-body p-6">

            <!-- Indicateur de chargement -->
            <div id="modal-loading" class="flex flex-col items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mb-4"></div>
                <p class="text-gray-600">Chargement des détails...</p>
            </div>

            <!-- Contenu masqué pendant le chargement -->
            <div id="modal-content" class="hidden space-y-6">

                <!-- Info du trajet -->
                <div class="trip-details-section">
                    <h4 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-route mr-2 text-green-500"></i>Informations sur le trajet
                    </h4>

                    <!-- Route et dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="trip-details-departure flex flex-col items-center md:items-start">
                            <h5 class="font-semibold text-gray-700 mb-2">Départ</h5>
                            <div class="space-y-2">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar mr-2 text-green-500"></i>
                                    <span id="modal-departure-date"></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-clock mr-2 text-green-500"></i>
                                    <span id="modal-departure-time"></span>
                                </div>
                            </div>
                        </div>
                        <div class="trip-details-arrival flex flex-col items-center md:items-end">
                            <h5 class="font-semibold text-gray-700 mb-2">Arrivée</h5>
                            <div class="space-y-2 flex flex-col md:items-end">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar mr-2 text-green-500"></i>
                                    <span id="modal-arrival-date"></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-clock mr-2 text-green-500"></i>
                                    <span id="modal-arrival-time"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Villes -->
                    <div class="trip-route-details text-center mb-6">
                        <div class="flex items-center justify-center text-2xl font-bold text-gray-800">
                            <span id="modal-city-dep" class="text-green-600"></span>
                            <span class="mx-4 text-gray-400">→</span>
                            <span id="modal-city-arr" class="text-green-600"></span>
                        </div>
                    </div>

                    <!-- Adresses complètes (départ et arrivée) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="address-group bg-gray-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-gray-700 mb-2">Adresse de départ</h5>
                            <div class="space-y-1 text-gray-600">
                                <p id="modal-departure-address"></p>
                                <p id="modal-add-dep-address"></p>
                                <p id="modal-postal-code-dep"></p>
                            </div>
                        </div>
                        <div class="address-group bg-gray-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-gray-700 mb-2">Adresse d'arrivée</h5>
                            <div class="space-y-1 text-gray-600">
                                <p id="modal-arrival-address"></p>
                                <p id="modal-add-arr-address"></p>
                                <p id="modal-postal-code-arr"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Info supp -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="trip-info-item bg-blue-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-blue-700 mb-2">Places disponibles</h5>
                            <p id="modal-n-tickets" class="text-2xl font-bold text-blue-600"></p>
                        </div>
                        <div class="trip-info-item bg-purple-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-purple-700 mb-2">Durée maximale</h5>
                            <p id="modal-max-travel-time" class="text-2xl font-bold text-purple-600"></p>
                        </div>
                        <div class="trip-info-item bg-green-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-green-700 mb-2">Prix</h5>
                            <p class="text-2xl font-bold text-green-600">
                                <span id="modal-price"></span> crédits
                            </p>
                        </div>
                    </div>

                    <!-- Eco ou non -->
                    <div class="mt-4 text-center">
                        <div id="modal-eco-travel"></div>
                    </div>
                </div>

                <!-- Conducteur -->
                <div class="driver-details-section">
                    <h4 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-user mr-2 text-green-500"></i>Informations sur le conducteur
                    </h4>

                    <div class="driver-profile flex items-center mb-6">
                        <div id="modal-driver-photo"
                            class="w-20 h-20 rounded-full border-4 border-green-400 shadow-md mr-4 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-gray-500"></i>
                        </div>
                        <div class="driver-info">
                            <h5 id="modal-driver-pseudo" class="text-xl font-semibold text-gray-800"></h5>
                            <div class="driver-rating flex items-center gap-2 mt-1">
                                <span id="modal-driver-rating" class="font-bold text-yellow-500"></span>
                                <span id="modal-driver-stars" class="text-yellow-500"></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Véhicule -->
                        <div class="details-vehicle bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-700 mb-3">Véhicule</h5>
                            <div class="vehicle-info space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Immatriculation :</span>
                                    <span id="modal-immat" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Marque :</span>
                                    <span id="modal-brand" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Modèle :</span>
                                    <span id="modal-model" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Couleur :</span>
                                    <span id="modal-color" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Énergie :</span>
                                    <span id="modal-energie" class="font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Préférences -->
                        <div class="driver-preferences bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-700 mb-3">Préférences</h5>
                            <div class="preferences-list space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Fumeur :</span>
                                    <span id="modal-pref-smoke" class="font-medium"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Animaux :</span>
                                    <span id="modal-pref-pet" class="font-medium"></span>
                                </div>
                                <div id="modal-pref-libre-container" class="flex justify-between">
                                    <span class="text-gray-600">Autres :</span>
                                    <span id="modal-pref-libre" class="font-medium"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avis -->
                <div class="reviews-section">
                    <h4 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-star mr-2 text-green-500"></i>Avis sur le conducteur
                    </h4>
                    <div class="reviews-container">
                        <div id="modal-reviews-list" class="space-y-4">
                            <div class="text-center text-gray-500 py-8">Chargement des avis...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer flex justify-between items-center p-6 border-t border-gray-200 bg-gray-50">
            <div id="modal-button-loading" class="hidden">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-500"></div>
            </div>
            <div class="flex space-x-3 ml-auto">
                <button
                    class="modal-close px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded transition-colors duration-300">
                    Fermer
                </button>
                <a href="#"
                    class="btn-participate modal-participate-btn-js hidden px-6 py-2 text-white font-bold rounded transition-colors duration-300">
                    Participer
                </a>
            </div>
        </div>
    </div>
</div>
