<div id="confirm-early-start-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    aria-labelledby="confirm-early-start-title" role="dialog" aria-modal="true">
    <div class="modal-overlay absolute inset-0"></div>
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="confirm-early-start-title" class="text-xl font-bold text-gray-900">Démarrage anticipé</h3>
            <button type="button" class="modal-close text-gray-400 hover:text-gray-500 transition-colors">
                <span class="sr-only">Fermer</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="mt-4">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Vous êtes sur le point de démarrer ce covoiturage en avance par rapport à l'heure prévue.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-gray-700 mb-2">
                    <strong>Heure de départ prévue :</strong> <span id="early-start-scheduled-time" class="text-blue-600"></span>
                </p>
                <p class="text-gray-700 mb-4">
                    Êtes-vous sûr de vouloir démarrer maintenant ? Tous les passagers prévus sont-ils bien arrivés ?
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" class="modal-close px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Annuler
                </button>
                <button type="button" id="confirm-early-start-btn" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                    <i class="fas fa-play mr-2"></i>Démarrer
                </button>
            </div>
        </div>
    </div>
</div>

