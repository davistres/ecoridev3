<!-- Pop-up add-vehicle-modal -->
<div id="add-vehicle-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    onclick="closeModal('add-vehicle-modal')">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 overflow-y-auto max-h-screen"
        onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Ajouter un véhicule</h2>
            <button onclick="closeModal('add-vehicle-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>

        <!-- Body -->
        <form id="addVehicleForm" action="{{ route('voitures.store') }}" method="POST">
            @csrf

            <!--Honeypot-->
            <div class="hidden">
                <label for="add_vehicle_details">Vehicle Details</label>
                <input type="text" id="add_vehicle_details" name="add_vehicle_details" tabindex="-1"
                    autocomplete="off">
            </div>

            <h3 class="text-xl font-semibold text-gray-700 mb-2">Informations véhicule</h3>
            <p class="text-sm text-slate-600 mb-4">Veuillez remplir tous les champs correctement.</p>

            <div id="add-vehicle-errors" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4"
                role="alert">
                <p class="font-bold">Des erreurs ont été détectées :</p>
                <ul class="list-disc list-inside"></ul>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="add-brand" class="block font-semibold text-gray-700">Marque</label>
                    <input type="text" id="add-brand" name="brand" maxlength="12" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 12 caractères.</small>
                </div>
                <div>
                    <label for="add-model" class="block font-semibold text-gray-700">Modèle</label>
                    <input type="text" id="add-model" name="model" maxlength="24" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 24 caractères.</small>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="add-immat" class="block font-semibold text-gray-700">Immatriculation</label>
                    <input type="text" id="add-immat" name="immat" maxlength="9" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Format: AA-123-BB ou AA123BB. Doit être unique.</small>
                    <p id="add-immat-error" class="text-red-500 text-xs mt-1"></p>
                </div>
                <div>
                    <label for="add-date_first_immat" class="block font-semibold text-gray-700">Date de la 1ère
                        immatriculation</label>
                    <input type="date" id="add-date_first_immat" name="date_first_immat" required
                        max="{{ date('Y-m-d') }}"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Ne peut pas être une date future.</small>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="add-color" class="block font-semibold text-gray-700">Couleur</label>
                    <input type="text" id="add-color" name="color" maxlength="12" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 12 caractères.</small>
                </div>
                <div>
                    <label for="add-n_place" class="block font-semibold text-gray-700">Nombre de places</label>
                    <input type="number" id="add-n_place" name="n_place" min="2" max="9" value="2"
                        required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Minimum 2, maximum 9.</small>
                </div>
                <div>
                    <label for="add-energie" class="block font-semibold text-gray-700">Type d’énergie</label>
                    <select id="add-energie" name="energie" required
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
                <button type="button" onclick="closeModal('add-vehicle-modal')"
                    class="px-4 py-2 text-sm font-semibold text-white bg-slate-500 rounded-lg hover:bg-slate-600 transition-colors duration-300">Annuler</button>
                <button type="submit"
                    class="px-5 py-2 bg-[#2ecc71] text-white font-semibold rounded-md hover:bg-[#27ae60]">Ajouter le
                    véhicule</button>
            </div>
        </form>
    </div>
</div>
