<!-- Modale "Mon covoiturage" pour les covoit proposés par un conducteur -->
<div class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center hidden z-50" id="myTripModal" role="dialog" aria-modal="true" aria-labelledby="myTripModalTitle">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div
        class="modal-container bg-white w-11/12 md:max-w-3xl mx-auto rounded-lg shadow-xl z-50 overflow-y-auto max-h-screen">

        <!-- Header -->
        <div class="modal-header flex justify-between items-center p-6 border-b border-gray-200">
            <h2 id="myTripModalTitle" class="text-2xl font-bold text-gray-800">Mon covoiturage</h2>
            <button class="modal-close text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="modal-body p-6">

            <!-- Contenu -->
            <div id="my-trip-content" class="space-y-6">

                <!-- Info du trajet -->
                <section aria-labelledby="my-trip-info-title" class="trip-details-section">
                    <h4 id="my-trip-info-title" class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-route mr-2 text-green-500"></i>Informations sur le trajet
                    </h4>

                    <!-- Route et dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="trip-details-departure flex flex-col items-center md:items-start">
                            <h5 class="font-semibold text-gray-700 mb-2">Départ</h5>
                            <div class="space-y-2">
                                <div class="flex items-center justify-center md:justify-start text-gray-600">
                                    <i class="fas fa-calendar mr-2 text-green-500"></i>
                                    <span id="my-trip-departure-date"></span>
                                </div>
                                <div class="flex items-center justify-center md:justify-start text-gray-600">
                                    <i class="fas fa-clock mr-2 text-green-500"></i>
                                    <span id="my-trip-departure-time"></span>
                                </div>
                            </div>
                        </div>
                        <div class="trip-details-arrival flex flex-col items-center md:items-end">
                            <h5 class="font-semibold text-gray-700 mb-2">Arrivée</h5>
                            <div class="space-y-2">
                                <div class="flex items-center justify-center md:justify-end text-gray-600">
                                    <i class="fas fa-calendar mr-2 text-green-500"></i>
                                    <span id="my-trip-arrival-date"></span>
                                </div>
                                <div class="flex items-center justify-center md:justify-end text-gray-600">
                                    <i class="fas fa-clock mr-2 text-green-500"></i>
                                    <span id="my-trip-arrival-time"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Villes -->
                    <div class="trip-route-details text-center mb-6">
                        <div class="flex items-center justify-center text-2xl font-bold text-gray-800">
                            <span id="my-trip-city-dep" class="text-green-600"></span>
                            <span class="mx-4 text-gray-400">→</span>
                            <span id="my-trip-city-arr" class="text-green-600"></span>
                        </div>
                    </div>

                    <!-- Adresses (départ et arrivée) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="address-group bg-gray-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-gray-700 mb-2">Adresse de départ</h5>
                            <div class="space-y-1 text-gray-600">
                                <p id="my-trip-departure-address"></p>
                                <p id="my-trip-add-dep-address"></p>
                                <p id="my-trip-postal-code-dep"></p>
                            </div>
                        </div>
                        <div class="address-group bg-gray-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-gray-700 mb-2">Adresse d'arrivée</h5>
                            <div class="space-y-1 text-gray-600">
                                <p id="my-trip-arrival-address"></p>
                                <p id="my-trip-add-arr-address"></p>
                                <p id="my-trip-postal-code-arr"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Info supp -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="trip-info-item bg-blue-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-blue-700 mb-2">Places proposées</h5>
                            <p id="my-trip-n-tickets" class="text-2xl font-bold text-blue-600"></p>
                        </div>
                        <div class="trip-info-item bg-purple-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-purple-700 mb-2">Durée maximale</h5>
                            <p id="my-trip-max-travel-time" class="text-2xl font-bold text-purple-600"></p>
                        </div>
                        <div class="trip-info-item bg-green-50 p-4 rounded-lg text-center">
                            <h5 class="font-semibold text-green-700 mb-2">Prix</h5>
                            <p class="text-2xl font-bold text-green-600">
                                <span id="my-trip-price"></span> crédits
                            </p>
                        </div>
                    </div>

                    <!-- Eco ou non -->
                    <div class="mt-4 text-center">
                        <div id="my-trip-eco-travel"></div>
                    </div>
                </section>

                <!-- Véhicule -->
                <section aria-labelledby="my-trip-vehicle-info-title" class="vehicle-details-section">
                    <h4 id="my-trip-vehicle-info-title" class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-car mr-2 text-green-500"></i>Informations sur le véhicule
                    </h4>

                    <div class="details-vehicle bg-gray-50 p-4 rounded-lg">
                        <div class="vehicle-info space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Immatriculation :</span>
                                <span id="my-trip-immat" class="font-medium"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Marque :</span>
                                <span id="my-trip-brand" class="font-medium"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Modèle :</span>
                                <span id="my-trip-model" class="font-medium"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Couleur :</span>
                                <span id="my-trip-color" class="font-medium"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Énergie :</span>
                                <span id="my-trip-energie" class="font-medium"></span>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer flex justify-end items-center p-6 border-t border-gray-200 bg-gray-50">
            <button
                class="modal-close px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded transition-colors duration-300">
                Fermer
            </button>
        </div>
    </div>
</div>
