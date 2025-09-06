@csrf

<!-- Honeypot -->
<div class="hidden">
    <label for="{{ $prefix }}_user_preferences">Préférences</label>
    <input type="text" id="{{ $prefix }}_user_preferences" name="user_preferences" tabindex="-1"
        autocomplete="off">
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Colonne de gauche -->
    <div class="space-y-4">
        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2">Lieu de départ</h4>
        <div>
            <label for="{{ $prefix }}_departure_address" class="block font-semibold text-gray-700">Adresse de
                départ*</label>
            <div class="flex items-center">
                <input type="text" id="{{ $prefix }}_departure_address" name="departure_address" required
                    maxlength="120" oninput="validateFirstChar(this)"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Maximum 120 caractères.</span>
                </div>
            </div>
        </div>
        <div>
            <label for="{{ $prefix }}_add_dep_address" class="block font-semibold text-gray-700">Complément
                d'adresse</label>
            <div class="flex items-center">
                <input type="text" id="{{ $prefix }}_add_dep_address" name="add_dep_address" maxlength="120"
                    oninput="validateFirstChar(this)"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Maximum 120 caractères.</span>
                </div>
            </div>
        </div>
        <div>
            <label for="{{ $prefix }}_postal_code_dep" class="block font-semibold text-gray-700">Code
                postal*</label>
            <div class="flex items-center">
                <input type="text" id="{{ $prefix }}_postal_code_dep" name="postal_code_dep" required
                    maxlength="6" oninput="formatPostalCode(this)"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Format: 12345 ou 12 345. Si 3ème caractère = espace, alors 6 chiffres
                        total.</span>
                </div>
            </div>
            <small id="{{ $prefix }}_postal_code_dep-error" class="text-red-600 mt-2"></small>
        </div>
        <div>
            <label for="{{ $prefix }}_city_dep" class="block font-semibold text-gray-700">Ville*</label>
            <div class="flex items-center">
                <input type="text" id="{{ $prefix }}_city_dep" name="city_dep" required maxlength="45"
                    oninput="formatCityName(this)"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Maximum 45 caractères.</span>
                </div>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 pt-4">Date et heure</h4>
        <div>
            <label for="{{ $prefix }}_departure_date" class="block font-semibold text-gray-700">Date de
                départ*</label>
            <div class="flex items-center">
                <input type="date" id="{{ $prefix }}_departure_date" name="departure_date" required
                    min="{{ date('Y-m-d') }}"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Les trajets de plus de 24h ne sont pas autorisés.</span>
                </div>
            </div>
        </div>
        <div>
            <label for="{{ $prefix }}_departure_time" class="block font-semibold text-gray-700">Heure de
                départ*</label>
            <div class="flex items-center">
                <input type="time" id="{{ $prefix }}_departure_time" name="departure_time" required
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Si le départ a lieu aujourd’hui, il doit y avoir au moins 6
                        heures d’écart pour qu’il soit pris en compte !</span>
                </div>
            </div>
            <small id="{{ $prefix }}_departure-time-error" class="text-red-600 mt-2"></small>
        </div>
        <div>
            <label for="{{ $prefix }}_arrival_date" class="block font-semibold text-gray-700">Date
                d'arrivée*</label>
            <div class="flex items-center">
                <input type="date" id="{{ $prefix }}_arrival_date" name="arrival_date" required disabled
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1 bg-gray-200">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Les trajets de plus de 24h ne sont pas autorisés.</span>
                </div>
            </div>
            <small id="{{ $prefix }}_arrival-date-error" class="text-red-600 mt-2"></small>
        </div>
        <div>
            <label for="{{ $prefix }}_arrival_time" class="block font-semibold text-gray-700">Heure
                d'arrivée*</label>
            <div class="flex items-center">
                <input type="time" id="{{ $prefix }}_arrival_time" name="arrival_time" required disabled
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1 bg-gray-200">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Un trajet doit durer au minimum 10 minutes.</span>
                </div>
            </div>
            <small id="{{ $prefix }}_arrival-time-error" class="text-red-600 mt-2"></small>
        </div>
        <div>
            <label for="{{ $prefix }}_max_travel_time" class="block font-semibold text-gray-700">Durée maximale
                duvoyage*</label>
            <div class="flex items-center">
                <input type="time" id="{{ $prefix }}_max_travel_time" name="max_travel_time" required
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Estimez la durée maximale de votre trajet en incluant les imprévus
                        (bouchons, travaux, etc.).</span>
                </div>
            </div>
            <small id="{{ $prefix }}_max-travel-time-error" class="text-red-600 mt-2"></small>
        </div>
    </div>

    <!-- Colonne de droite -->
    <div class="space-y-4">
        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2">Lieu d'arrivée</h4>
        <div>
            <label for="{{ $prefix }}_arrival_address" class="block font-semibold text-gray-700">Adresse
                d'arrivée*</label>
            <div class="flex items-center">
                <input type="text" id="{{ $prefix }}_arrival_address" name="arrival_address" required
                    maxlength="120" oninput="validateFirstChar(this)"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Maximum 120 caractères.</span>
                </div>
            </div>
        </div>
        <div>
            <label for="{{ $prefix }}_add_arr_address" class="block font-semibold text-gray-700">Complément
                d'adresse</label>
            <div class="flex items-center">
                <input type="text" id="{{ $prefix }}_add_arr_address" name="add_arr_address"
                    maxlength="120" oninput="validateFirstChar(this)"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Maximum 120 caractères.</span>
                </div>
            </div>
        </div>
        <div>
            <label for="{{ $prefix }}_postal_code_arr" class="block font-semibold text-gray-700">Code
                postal*</label>
            <div class="flex items-center">
                <input type="text" id="{{ $prefix }}_postal_code_arr" name="postal_code_arr" required
                    maxlength="6" oninput="formatPostalCode(this)"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Format: 12345 ou 12 345. Si 3ème caractère = espace, alors 6 chiffres
                        total.</span>
                </div>
            </div>
            <small id="{{ $prefix }}_postal_code_arr-error" class="text-red-600 mt-2"></small>
        </div>
        <div>
            <label for="{{ $prefix }}_city_arr" class="block font-semibold text-gray-700">Ville*</label>
            <div class="flex items-center">
                <input type="text" id="{{ $prefix }}_city_arr" name="city_arr" required maxlength="45"
                    oninput="formatCityName(this)"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="tooltip ml-2">
                    <span class="text-gray-500">ⓘ</span>
                    <span class="tooltiptext">Maximum 45 caractères.</span>
                </div>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 pt-4">Détails du trajet</h4>
        <div>
            <label for="{{ $prefix }}_voiture_id" class="block font-semibold text-gray-700">Véhicule*</label>
            <div class="flex items-center">
                <select name="voiture_id" id="{{ $prefix }}_voiture_id_select" required
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                    <option value="" disabled selected>Sélectionnez votre véhicule</option>
                    @foreach (Auth::user()->voitures as $voiture)
                        <option value="{{ $voiture->voiture_id }}" data-places="{{ $voiture->n_place }}">
                            {{ $voiture->brand }} {{ $voiture->model }} ({{ $voiture->immat }})
                        </option>
                    @endforeach
                    <optgroup label="──────────">
                        <option value="add_car" class="text-center bg-gray-200">[ Ajouter un véhicule ]
                        </option>
                    </optgroup>
                </select>
                <div class="w-4 ml-2"></div>
            </div>
        </div>
        <div>
            <label for="{{ $prefix }}_n_tickets" class="block font-semibold text-gray-700">Nombre de places
                proposées*</label>
            <div class="flex items-center">
                <input type="number" name="n_tickets" id="{{ $prefix }}_n_tickets_input" required
                    min="1"
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                <div class="w-4 ml-2"></div>
            </div>
            <small id="{{ $prefix }}_seats-helper" class="text-slate-500 mt-2"></small>
        </div>
        <div>
            <label for="{{ $prefix }}_price" class="block font-semibold text-gray-700">Prix par place*</label>
            <div class="flex items-center">
                <div class="relative w-full">
                    <input type="number" id="{{ $prefix }}_price" name="price" required min="2"
                        step="1"
                        onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) { event.preventDefault(); }"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1 pr-16">
                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">crédits</span>
                </div>
                <div class="w-4 ml-2"></div>
            </div>
            <small class="text-slate-500 mt-2">Dont 2 crédits de commission automatique.</small>
        </div>

    </div>
</div>

<!-- Message d'erreur pour la durée -->
<div id="{{ $prefix }}_duration-warning"
    class="hidden text-center bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-6"
    role="alert">
    <strong class="font-bold">Trajet trop long !</strong>
    <span class="block sm:inline">EcoRide est pour les trajets de moins de 24h. Pour des voyages plus longs, découvrez
        bientôt TripsEcoRide !</span>
</div>


<!-- Message d'erreur -->
<div id="{{ $prefix }}_address-error" class="text-red-600 mb-4 mt-8 text-center" style="display: none;"></div>
<div id="{{ $prefix }}_form-general-error" class="text-red-600 mb-4 text-center" style="display: none;"></div>

<!-- Footer -->
<div class="mt-8 flex justify-end space-x-4">
    <button type="button"
        onclick="closeModal('{{ $prefix == 'create' ? 'create-covoit-modal' : 'modif-covoit-modal' }}')"
        class="px-4 py-2 text-sm font-semibold text-white bg-slate-500 rounded-lg hover:bg-slate-600 transition-colors duration-300">Annuler</button>
    <button type="submit"
        class="px-5 py-2 bg-[#2ecc71] text-white font-semibold rounded-md hover:bg-[#27ae60]">{{ $prefix == 'create' ? 'Proposer le trajet' : 'Enregistrer les modifications' }}</button>
</div>
