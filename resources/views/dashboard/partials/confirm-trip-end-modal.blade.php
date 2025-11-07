<div id="confirm-trip-end-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
    aria-labelledby="confirm-trip-end-title" role="dialog" aria-modal="true">
    <div class="modal-overlay absolute inset-0"></div>
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="confirm-trip-end-title" class="text-xl font-bold text-gray-900">Confirmer la fin du covoiturage</h3>
            <button type="button" class="modal-close text-gray-400 hover:text-gray-500 transition-colors">
                <span class="sr-only">Fermer</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="mt-4">
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Vous êtes sur le point de marquer ce covoiturage comme terminé.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-gray-700 mb-2">
                    <strong>Trajet :</strong> <span id="trip-end-route" class="text-blue-600"></span>
                </p>
                <p class="text-gray-700 mb-4">
                    Confirmez-vous que le covoiturage est bien terminé et que tous les passagers sont arrivés à destination ?
                </p>
                <p class="text-sm text-gray-600 italic">
                    <i class="fas fa-envelope mr-1"></i>
                    Un email sera automatiquement envoyé aux passagers pour leur demander de remplir le formulaire de satisfaction obligatoire.
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" class="modal-close px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Annuler
                </button>
                <button type="button" id="confirm-trip-end-btn" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                    <i class="fas fa-check mr-2"></i>Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

