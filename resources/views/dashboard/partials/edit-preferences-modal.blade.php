<!-- Pop-up edit-preferences-modal -->
<div id="edit-preferences-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    onclick="closeModal('edit-preferences-modal')">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4"
        onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Modifier mes préférences</h2>
            <button onclick="closeModal('edit-preferences-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>

        <!-- Body -->
        <form id="editPreferencesForm" action="{{ route('profile.preferences.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <h3 class="text-xl font-semibold text-gray-700 mb-4">Préférences conducteur</h3>

            <div class="mb-4">
                <label class="block font-semibold text-gray-700">Préférence fumeur</label>
                <div class="mt-2 flex flex-wrap gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="pref_smoke" value="Fumeur" required
                            class="form-radio text-green-500 focus:ring-green-500"
                            {{ Auth::user()->pref_smoke === 'Fumeur' ? 'checked' : '' }}>
                        <span class="ml-2">Fumeur</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="pref_smoke" value="Non-fumeur" required
                            class="form-radio text-green-500 focus:ring-green-500"
                            {{ Auth::user()->pref_smoke === 'Non-fumeur' ? 'checked' : '' }}>
                        <span class="ml-2">Non fumeur</span>
                    </label>
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-semibold text-gray-700">Préférence animaux</label>
                <div class="mt-2 flex flex-wrap gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="pref_pet" value="Acceptés" required
                            class="form-radio text-green-500 focus:ring-green-500"
                            {{ Auth::user()->pref_pet === 'Acceptés' ? 'checked' : '' }}>
                        <span class="ml-2">Animaux acceptés</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="pref_pet" value="Non-acceptés" required
                            class="form-radio text-green-500 focus:ring-green-500"
                            {{ Auth::user()->pref_pet === 'Non-acceptés' ? 'checked' : '' }}>
                        <span class="ml-2">Animaux non acceptés</span>
                    </label>
                </div>
            </div>

            <div class="mb-4">
                <label for="edit-pref_libre" class="block font-semibold text-gray-700">Autres préférences ou
                    informations</label>
                <textarea id="edit-pref_libre" name="pref_libre" rows="3" maxlength="255"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1"
                    placeholder="Exemple: Musique classique, conversation sur le cinéma et l’art, etc...">{{ Auth::user()->pref_libre }}</textarea>
                <small class="text-slate-500">Optionnel. Maximum 255 caractères.</small>
            </div>

            <!-- Footer -->
            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="closeModal('edit-preferences-modal')"
                    class="px-4 py-2 text-sm font-semibold text-white bg-slate-500 rounded-lg hover:bg-slate-600 transition-colors duration-300">Annuler</button>
                <button type="submit"
                    class="px-5 py-2 bg-[#2ecc71] text-white font-semibold rounded-md hover:bg-[#27ae60]">Valider les changements</button>
            </div>
        </form>
    </div>
</div>
