<!-- Modale => redevenir PASSAGER supprimera tout (infos, voiture et covoit) -->
<div id="confirm-delete-all-for-change-role-to-passenger-modal"
    class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    role="dialog" aria-modal="true" aria-labelledby="confirmDeleteAllTitle" onclick="closeModal('confirm-delete-all-for-change-role-to-passenger-modal')">
    <div class="bg-white rounded-lg p-8 max-w-lg w-full mx-4" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 id="confirmDeleteAllTitle" class="text-2xl font-bold text-red-700">Attention !</h2>
            <button onclick="closeModal('confirm-delete-all-for-change-role-to-passenger-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>

        <!-- Body -->
        <div>
            <p class="text-slate-700 mb-4">
                Vous êtes sur le point de redevenir <span class="font-bold">PASSAGER</span>.
            </p>
            <p class="text-slate-700 mb-4">
                Si vous confirmez, vous ne pourrez plus proposer de covoiturages, vous perdrez les éventuels
                covoiturages en cours et toutes les informations liées à votre statut de conducteur seront
                définitivement supprimées.
            </p>
            <p class="font-semibold text-slate-800">
                Êtes-vous certain de vouloir continuer ?
            </p>
        </div>

        <!-- Footer -->
        <div class="mt-6 flex justify-end space-x-4">
            <button type="button" onclick="closeModal('confirm-delete-all-for-change-role-to-passenger-modal')"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Annuler</button>
            <button id="confirm-role-change-btn"
                class="px-4 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700">Oui, je suis
                certain</button>
        </div>
    </div>
</div>
