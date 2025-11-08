<div id="satisfaction-form-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    role="dialog" aria-modal="true" aria-labelledby="satisfactionFormModalTitle" onclick="closeModal('satisfaction-form-modal')">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 overflow-y-auto max-h-screen"
        onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 id="satisfactionFormModalTitle" class="text-2xl font-bold text-gray-800">Votre avis nous intéresse</h2>
            <button onclick="closeModal('satisfaction-form-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>

        <form id="satisfactionForm" data-submit-url="{{ route('satisfaction.store') }}">
            @csrf
            <input type="hidden" id="satisfaction_id" name="satisfaction_id" value="">
            <input type="hidden" id="covoit_id" name="covoit_id" value="">

            <div id="satisfaction-errors" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4"
                role="alert">
                <p class="font-bold">Des erreurs ont été détectées :</p>
                <ul class="list-disc list-inside"></ul>
            </div>

            <div class="mb-6">
                <p class="text-lg text-gray-700 mb-4">
                    Vous avez récemment participé au covoiturage de <strong id="driver-name-display"></strong> 
                    le <strong id="trip-date-display"></strong> de <strong id="trip-route-display"></strong>.
                </p>
            </div>

            <div class="mb-6">
                <label class="block font-semibold text-gray-700 mb-3">
                    Êtes-vous satisfait de ce covoiturage ? <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="feeling" value="1" required
                            class="w-5 h-5 text-green-600 focus:ring-green-500 border-gray-300">
                        <span class="ml-2 text-gray-700 font-medium">Oui</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="feeling" value="0" required
                            class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300">
                        <span class="ml-2 text-gray-700 font-medium">Non</span>
                    </label>
                </div>
            </div>

            <div class="mb-6" id="comment-section">
                <label for="comment" class="block font-semibold text-gray-700 mb-2">
                    Commentaire <span id="comment-required-indicator" class="text-red-500 hidden">*</span>
                </label>
                <textarea id="comment" name="comment" rows="5"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                    placeholder="Partagez votre expérience..."></textarea>
                <small class="text-slate-500">Exprimez-vous librement.</small>
            </div>

            <div class="mb-6">
                <label for="review" class="block font-semibold text-gray-700 mb-2">
                    Avis sur le conducteur
                </label>
                <textarea id="review" name="review" rows="5" maxlength="1200"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                    placeholder="Laissez un avis sur le conducteur (maximum 240 mots)..."></textarea>
                <small class="text-slate-500">Maximum 240 mots (environ 1200 caractères).</small>
            </div>

            <div class="mb-6" id="note-section">
                <label class="block font-semibold text-gray-700 mb-2">
                    Note <span id="note-required-indicator" class="text-red-500 hidden">*</span>
                </label>
                <div class="flex gap-2" id="star-rating">
                    <i class="far fa-star text-4xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors duration-200" data-rating="1"></i>
                    <i class="far fa-star text-4xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors duration-200" data-rating="2"></i>
                    <i class="far fa-star text-4xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors duration-200" data-rating="3"></i>
                    <i class="far fa-star text-4xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors duration-200" data-rating="4"></i>
                    <i class="far fa-star text-4xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors duration-200" data-rating="5"></i>
                </div>
                <input type="hidden" id="note" name="note" value="">
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="closeModal('satisfaction-form-modal')"
                    class="px-4 py-2 text-sm font-semibold text-white bg-slate-500 rounded-lg hover:bg-slate-600 transition-colors duration-300">
                    Annuler
                </button>
                <button type="submit" id="submit-satisfaction-btn"
                    class="px-4 py-2 bg-[#2ecc71] text-white font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg transition-all duration-300 hover:bg-[#27ae60]">
                    Valider
                </button>
            </div>
        </form>
    </div>
</div>

