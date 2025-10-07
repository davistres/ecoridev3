<!-- Pop-up driverinfo-modal -->
<div id="driverinfo-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    role="dialog" aria-modal="true" aria-labelledby="driverInfoModalTitle" onclick="closeModal('driverinfo-modal')">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 overflow-y-auto max-h-screen"
        onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 id="driverInfoModalTitle" class="text-2xl font-bold text-gray-800">Informations conducteur</h2>
            <button onclick="closeModal('driverinfo-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>

        <!-- Body -->
        <form id="driverInfoForm" action="{{ route('profile.driverinfo.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <!--Honeypot-->
            <div class="hidden">
                <label for="n_secu_social">Numéro de sécurité sociale</label>
                <input type="text" id="n_secu_social" name="n_secu_social" tabindex="-1" autocomplete="off">
            </div>

            <input type="hidden" name="new_role" id="new_role_input">

            <section aria-labelledby="driver-preferences-title">
                <h3 id="driver-preferences-title" class="text-xl font-semibold text-gray-700 mb-4">Préférence conducteur</h3>

                <div class="mb-4">
                    <label class="block font-semibold text-gray-700">Préférence fumeur <span class="text-red-500">*
                            Obligatoire</span></label>
                    <div class="mt-2 flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="pref_smoke" value="Fumeur" required
                                class="form-radio text-green-500 focus:ring-green-500">
                            <span class="ml-2">Fumeur</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="pref_smoke" value="Non-fumeur" required
                                class="form-radio text-green-500 focus:ring-green-500">
                            <span class="ml-2">Non fumeur</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block font-semibold text-gray-700">Préférence animaux <span class="text-red-500">*
                            Obligatoire</span></label>
                    <div class="mt-2 flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="pref_pet" value="Acceptés" required
                                class="form-radio text-green-500 focus:ring-green-500">
                            <span class="ml-2">Animaux acceptés</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="pref_pet" value="Non-acceptés" required
                                class="form-radio text-green-500 focus:ring-green-500">
                            <span class="ml-2">Animaux non acceptés</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="pref_libre" class="block font-semibold text-gray-700">Autres préférences ou
                        informations</label>
                    <textarea id="pref_libre" name="pref_libre" rows="3" maxlength="255"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1"
                        placeholder="Exemple: Musique classique, conversation sur le cinéma et l’art, etc..."></textarea>
                    <small class="text-slate-500">Optionnel. Maximum 255 caractères.</small>
                </div>

                <div class="mb-6">
                    <label for="driver_profile_photo" class="block font-semibold text-gray-700">Photo de profil</label>
                    <input type="file" id="driver_profile_photo" name="profile_photo" accept="image/png,image/jpeg"
                        class="w-full border rounded-md p-2 mt-1 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    <small class="text-slate-500">Optionnel (2 Mo max). Une photo de profil aide à établir la confiance
                        avec vos
                        passagers.</small>
                    <small id="profile_photo_error" class="text-red-500 mt-1 block"></small>
                </div>
            </section>

            <hr class="my-6">

            <section aria-labelledby="driver-vehicle-info-title">
                <h3 id="driver-vehicle-info-title" class="text-xl font-semibold text-gray-700 mb-2">Informations véhicule</h3>
                <p><span class="font-semibold text-red-500"> Toutes les informations suivantes sont
                        obligatoires.</span> <span class="text-sm text-slate-600 mb-4">Veuillez remplir tous
                        les champs correctement.</span></p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="brand" class="block font-semibold text-gray-700">Marque</label>
                        <input type="text" id="brand" name="brand" maxlength="12" required
                            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                        <small class="text-slate-500">Maximum 12 caractères.</small>
                    </div>
                    <div>
                        <label for="model" class="block font-semibold text-gray-700">Modèle</label>
                        <input type="text" id="model" name="model" maxlength="24" required
                            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                        <small class="text-slate-500">Maximum 24 caractères.</small>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="immat" class="block font-semibold text-gray-700">Immatriculation</label>
                        <input type="text" id="immat" name="immat" maxlength="9" required
                            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                        <small class="text-slate-500">Format: AA-123-BB ou AB123CD.</small>
                        <small id="immat_error" class="text-red-500 mt-1 block"></small>
                    </div>
                    <div>
                        <label for="date_first_immat" class="block font-semibold text-gray-700">Date de la 1ère
                            immatriculation</label>
                        <input type="date" id="date_first_immat" name="date_first_immat" required
                            max="{{ date('Y-m-d') }}"
                            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                        <small class="text-slate-500">Ne peut pas être une date future.</small>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="color" class="block font-semibold text-gray-700">Couleur dominante (une)</label>
                        <input type="text" id="color" name="color" maxlength="12" required
                            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                        <small class="text-slate-500">Maximum 12 caractères.</small>
                    </div>
                    <div>
                        <label for="n_place" class="block font-semibold text-gray-700">Nombre de places</label>
                        <input type="number" id="n_place" name="n_place" min="2" max="9"
                            value="2" required
                            class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                        <small class="text-slate-500">Minimum 2, maximum 9.</small>
                    </div>
                    <div>
                        <label for="energie" class="block font-semibold text-gray-700">Type d’énergie</label>
                        <select id="energie" name="energie" required
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
            </section>

            <!-- Footer -->
            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="closeModal('driverinfo-modal')"
                    class="px-4 py-2 text-sm font-semibold text-white bg-slate-500 rounded-lg hover:bg-slate-600 transition-colors duration-300">Annuler</button>
                <button type="submit"
                    class="px-5 py-2 bg-[#2ecc71] text-white font-semibold rounded-md hover:bg-[#27ae60]">Changer de
                    rôle</button>
            </div>
        </form>
    </div>
</div>
