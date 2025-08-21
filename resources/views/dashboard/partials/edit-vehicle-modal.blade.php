<!-- Pop-up edit-vehicle-modal -->
<div id="edit-vehicle-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    onclick="closeModal('edit-vehicle-modal')">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 overflow-y-auto max-h-screen"
        onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Modifier le véhicule</h2>
            <button onclick="closeModal('edit-vehicle-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>

        <!-- Body -->
        <form id="editVehicleForm" method="POST">
            @csrf
            @method('PUT')

            <!--Honeypot-->
            <div class="hidden">
                <label for="vehicle_details">Vehicle Details</label>
                <input type="text" id="vehicle_details" name="vehicle_details" tabindex="-1" autocomplete="off">
            </div>

            <h3 class="text-xl font-semibold text-gray-700 mb-2">Informations véhicule</h3>
            <p class="text-sm text-slate-600 mb-4">Veuillez remplir tous les champs correctement.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit-brand" class="block font-semibold text-gray-700">Marque</label>
                    <input type="text" id="edit-brand" name="brand" maxlength="12" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 12 caractères.</small>
                </div>
                <div>
                    <label for="edit-model" class="block font-semibold text-gray-700">Modèle</label>
                    <input type="text" id="edit-model" name="model" maxlength="24" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 24 caractères.</small>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit-immat" class="block font-semibold text-gray-700">Immatriculation</label>
                    <input type="text" id="edit-immat" name="immat" maxlength="10" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 10 caractères. Doit être unique.</small>
                </div>
                <div>
                    <label for="edit-date_first_immat" class="block font-semibold text-gray-700">Date de la 1ère
                        immatriculation</label>
                    <input type="date" id="edit-date_first_immat" name="date_first_immat" required
                        max="{{ date('Y-m-d') }}"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Ne peut pas être une date future.</small>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="edit-color" class="block font-semibold text-gray-700">Couleur</label>
                    <input type="text" id="edit-color" name="color" maxlength="12" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 12 caractères.</small>
                </div>
                <div>
                    <label for="edit-n_place" class="block font-semibold text-gray-700">Nombre de places</label>
                    <input type="number" id="edit-n_place" name="n_place" min="2" max="9" value="2"
                        required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Minimum 2, maximum 9.</small>
                </div>
                <div>
                    <label for="edit-energie" class="block font-semibold text-gray-700">Type d’énergie</label>
                    <select id="edit-energie" name="energie" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                        <option value="" disabled selected>Choisir...</option>
                        <option value="Electrique">Electrique</option>
                        <option value="Hybride">Hybride</option>
                        <option value="Diesel/Gazole">Diesel/Gazole</option>
                        <option value="Essence">Essence</option>
                        <option value="GPL">GPL</option>
                    </select>
                    <small class="text-slate-500">Les véhicules électriques sont considérés comme écologiques.</small>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="closeModal('edit-vehicle-modal')"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Annuler</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600">Enregistrer les
                    modifications</button>
            </div>
        </form>
    </div>
</div>
