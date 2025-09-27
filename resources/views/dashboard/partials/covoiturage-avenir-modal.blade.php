<!-- Modale "Mes covoiturages à venir" = "Trajet planifié" -->
<div class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center hidden z-50"
    id="covoiturage-avenir-modal">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

    <div
        class="modal-container bg-white w-11/12 md:max-w-4xl mx-auto rounded-lg shadow-xl z-50 overflow-y-auto max-h-screen">

        <!-- Header -->
        <div class="modal-header flex justify-between items-center p-6 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-800">Trajet planifié</h3>
            <button class="modal-close text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="modal-body p-6">
            <div id="modal-avenir-content" class="space-y-6">
                <!-- Message de rappel -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-lg text-gray-700">
                        Bonjour <span class="font-semibold text-green-600" id="modal-avenir-user-name"></span> !
                    </p>
                    <p class="text-base text-gray-600 mt-2" id="modal-avenir-recap-text"></p>
                </div>

                <!-- Info du trajet -->
                <div class="trip-details-section">
                    <h4 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-route mr-2 text-green-500"></i>Informations sur le trajet
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="flex flex-col items-center md:items-start">
                            <h5 class="font-semibold text-gray-700 mb-2">Départ</h5>
                            <div class="space-y-2">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar mr-2 text-green-500"></i>
                                    <span id="modal-avenir-departure-date"></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-clock mr-2 text-green-500"></i>
                                    <span id="modal-avenir-departure-time"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-center md:items-start">
                            <h5 class="font-semibold text-gray-700 mb-2">Arrivée</h5>
                            <div class="space-y-2">
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar mr-2 text-green-500"></i>
                                    <span id="modal-avenir-arrival-date"></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-clock mr-2 text-green-500"></i>
                                    <span id="modal-avenir-arrival-time"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h5 class="font-semibold text-gray-700 mb-2">Adresse de départ</h5>
                            <div class="flex items-start text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2 text-green-500 mt-1"></i>
                                <span id="modal-avenir-departure-address"></span>
                            </div>
                        </div>
                        <div>
                            <h5 class="font-semibold text-gray-700 mb-2">Adresse d'arrivée</h5>
                            <div class="flex items-start text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2 text-green-500 mt-1"></i>
                                <span id="modal-avenir-arrival-address"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info du conducteur -->
                <div class="driver-details-section">
                    <h4 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-user mr-2 text-green-500"></i>Informations sur le conducteur
                    </h4>
                    <div class="flex items-center space-x-4">
                        <div id="modal-avenir-driver-photo"
                            class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center border-2 border-green-500">
                            <i class="fas fa-user text-gray-600 text-xl"></i>
                        </div>
                        <div>
                            <h5 class="font-semibold text-gray-800 text-lg" id="modal-avenir-driver-name"></h5>
                            <div class="flex items-center" id="modal-avenir-driver-rating"></div>
                        </div>
                    </div>
                </div>

                <!-- Info du véhicule -->
                <div class="vehicle-details-section">
                    <h4 class="text-xl font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-car mr-2 text-green-500"></i>Informations sur le véhicule
                    </h4>
                    <div class="space-y-3">
                        <p><strong>Marque :</strong> <span id="modal-avenir-car-brand"></span></p>
                        <p><strong>Modèle :</strong> <span id="modal-avenir-car-model"></span></p>
                        <p><strong>Couleur :</strong> <span id="modal-avenir-car-color"></span></p>
                        <p><strong>Énergie :</strong> <span id="modal-avenir-car-energy"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer flex justify-end items-center p-6 border-t border-gray-200 bg-gray-50">
            <button
                class="modal-close px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded transition-colors duration-300">
                Fermer
            </button>
            <button id="modal-avenir-cancel-btn" data-confirmation-id=""
                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded transition-colors duration-300 ml-4">
                Annuler ma participation
            </button>
        </div>
    </div>
</div>
