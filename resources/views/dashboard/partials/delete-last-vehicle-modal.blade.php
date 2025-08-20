<!-- Pop-up delete-last-vehicle-modal -->
<div id="delete-last-vehicle-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    onclick="closeModal('delete-last-vehicle-modal')">
    <div class="bg-white rounded-lg p-8 max-w-lg w-full mx-4" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-start mb-4">
            <div class="mr-4 text-red-500">
                <i class="fas fa-exclamation-triangle fa-2x"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Attention, dernière chance !</h2>
            </div>
        </div>

        <!-- Body -->
        <div class="text-slate-600 space-y-3">
            <p>
                Vous êtes sur le point de supprimer votre dernier véhicule.
            </p>
            <p>
                Si vous confirmez, votre rôle sera automatiquement changé en
                <span class="font-bold text-slate-800">"Passager"</span>.
                Vous ne pourrez plus proposer de covoiturages et toutes les informations liées à votre statut de conducteur seront définitivement supprimées.
            </p>
            <p class="font-semibold mt-4">
                Êtes-vous certain de vouloir continuer ?
            </p>
        </div>

        <!-- Footer -->
        <div class="mt-8 flex justify-end space-x-4">
            <button type="button" onclick="closeModal('delete-last-vehicle-modal')"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Annuler</button>
            <button id="confirm-delete-last-vehicle-btn" type="button"
                class="px-5 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700">Confirmer la suppression</button>
        </div>
    </div>
</div>
