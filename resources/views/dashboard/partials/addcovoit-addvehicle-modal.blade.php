<!-- Pop-up addcovoit-addvehicle-modal -->
<div id="addcovoit-addvehicle-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    onclick="closeAddCovoitVehicleModal()">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 overflow-y-auto max-h-screen"
        onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Ajouter un véhicule</h2>
            <button onclick="closeAddCovoitVehicleModal()"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>

        <!-- Body -->
        <form id="addCovoitVehicleForm">
            @csrf

            <!--Honeypot-->
            <div class="hidden">
                <label for="addcovoit_vehicle_details">Vehicle Details</label>
                <input type="text" id="addcovoit_vehicle_details" name="add_vehicle_details" tabindex="-1"
                    autocomplete="off">
            </div>

            <h3 class="text-xl font-semibold text-gray-700 mb-2">Informations véhicule</h3>
            <p class="text-sm text-slate-600 mb-4">Veuillez remplir tous les champs correctement.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="addcovoit-brand" class="block font-semibold text-gray-700">Marque</label>
                    <input type="text" id="addcovoit-brand" name="brand" maxlength="12" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 12 caractères.</small>
                </div>
                <div>
                    <label for="addcovoit-model" class="block font-semibold text-gray-700">Modèle</label>
                    <input type="text" id="addcovoit-model" name="model" maxlength="24" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 24 caractères.</small>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="addcovoit-immat" class="block font-semibold text-gray-700">Immatriculation</label>
                    <input type="text" id="addcovoit-immat" name="immat" maxlength="9" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Format: AA-123-BB ou AA123BB. Doit être unique.</small>
                    <small id="addcovoit-immat_error" class="text-red-500 mt-1 block"></small>
                </div>
                <div>
                    <label for="addcovoit-date_first_immat" class="block font-semibold text-gray-700">Date de la 1ère
                        immatriculation</label>
                    <input type="date" id="addcovoit-date_first_immat" name="date_first_immat" required
                        max="{{ date('Y-m-d') }}"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Ne peut pas être une date future.</small>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="addcovoit-color" class="block font-semibold text-gray-700">Couleur</label>
                    <input type="text" id="addcovoit-color" name="color" maxlength="12" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Maximum 12 caractères.</small>
                </div>
                <div>
                    <label for="addcovoit-n_place" class="block font-semibold text-gray-700">Nombre de places</label>
                    <input type="number" id="addcovoit-n_place" name="n_place" min="2" max="9" value="2"
                        required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <small class="text-slate-500">Minimum 2, maximum 9.</small>
                </div>
                <div>
                    <label for="addcovoit-energie" class="block font-semibold text-gray-700">Type d'énergie</label>
                    <select id="addcovoit-energie" name="energie" required
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                        <option value="">Sélectionnez le type d'énergie</option>
                        <option value="Electrique">Électrique</option>
                        <option value="Hybride">Hybride</option>
                        <option value="Diesel/Gazole">Diesel/Gazole</option>
                        <option value="Essence">Essence</option>
                        <option value="GPL">GPL</option>
                    </select>
                    <small class="text-slate-500">Type de carburant du véhicule.</small>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-3 pt-6 border-t">
                <button type="button" onclick="closeAddCovoitVehicleModal()"
                    class="px-6 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    Ajouter le véhicule
                </button>
            </div>
        </form>
    </div>
</div>
