<!-- Modale => Suppr vehicule avec covoit -->
<div id="confirm-delete-vehicule-with-covoit-modal"
    class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    onclick="closeModal('confirm-delete-vehicule-with-covoit-modal')">
    <div class="bg-white rounded-lg p-8 max-w-lg w-full mx-4" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-red-700">Attention !</h2>
            <button onclick="closeModal('confirm-delete-vehicule-with-covoit-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>

        <!-- Body -->
        <div>
            <p class="text-slate-700 mb-4">
                Ce véhicule est lié à un ou des covoiturages futurs.
            </p>
            <p class="text-slate-700 mb-4">
                Si vous le supprimez, les trajets programmés seront eux aussi effacés.
            </p>
            <p class="font-semibold text-slate-800">
                Êtes-vous certain de vouloir continuer ?
            </p>
        </div>

        <!-- Footer -->
        <div class="mt-6 flex justify-end space-x-4">
            <button type="button" onclick="closeModal('confirm-delete-vehicule-with-covoit-modal')"
                class="px-4 py-2 text-sm font-semibold text-white bg-slate-500 rounded-lg hover:bg-slate-600 transition-colors duration-300">Annuler</button>
            <button id="confirm-delete-with-carpools-btn"
                class="px-4 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700">Oui, je suis
                certain</button>
        </div>
    </div>
</div>
