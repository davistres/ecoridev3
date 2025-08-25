<!-- Pop-up pour créer un covoit-->
<div id="create-covoit-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    onclick="closeModal('create-covoit-modal')">
    <div class="bg-white rounded-lg p-8 max-w-3xl w-full mx-4 overflow-y-auto max-h-screen"
        onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Proposer un covoiturage</h2>
            <button onclick="closeModal('create-covoit-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>
        <p class="text-gray-600 mb-4">Veuillez remplir tous les champs correctement en respectant les indications.</p>
        <p class="text-sm text-red-600 mb-6">Tous les champs ayant un astérisque (*) sont OBLIGATOIRES !</p>

        <!-- Body -->
        <form id="createCovoitForm" action="{{ route('covoiturages.store') }}" method="POST"
            onsubmit="return validateCovoitForm()">
            @csrf

            <!-- Honeypot -->
            <div class="hidden">
                <label for="user_preferences">Préférences</label>
                <input type="text" id="user_preferences" name="user_preferences" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Colonne de gauche -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800 border-b pb-2">Lieu de départ</h4>
                    <div>
                        <label for="departure_address" class="block font-semibold text-gray-700">Adresse de
                            départ*</label>
                        <div class="flex items-center">
                            <input type="text" name="departure_address" required maxlength="120"
                                oninput="validateFirstChar(this)"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">Maximum 120 caractères.</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="add_dep_address" class="block font-semibold text-gray-700">Complément
                            d'adresse</label>
                        <div class="flex items-center">
                            <input type="text" name="add_dep_address" maxlength="120"
                                oninput="validateFirstChar(this)"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">Maximum 120 caractères.</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="postal_code_dep" class="block font-semibold text-gray-700">Code postal*</label>
                        <div class="flex items-center">
                            <input type="text" name="postal_code_dep" required maxlength="6"
                                oninput="formatPostalCode(this)"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">5 ou 6 chiffres, un espace autorisé.</span>
                            </div>
                        </div>
                        <small id="postal_code_dep-error" class="text-red-600 mt-2"></small>
                    </div>
                    <div>
                        <label for="city_dep" class="block font-semibold text-gray-700">Ville*</label>
                        <div class="flex items-center">
                            <input type="text" name="city_dep" required maxlength="45" oninput="formatCityName(this)"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">Maximum 45 caractères.</span>
                            </div>
                        </div>
                    </div>

                    <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 pt-4">Date et heure</h4>
                    <div>
                        <label for="departure_date" class="block font-semibold text-gray-700">Date de départ*</label>
                        <div class="flex items-center">
                            <input type="date" name="departure_date" required min="{{ date('Y-m-d') }}"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="w-4 ml-2"></div>
                        </div>
                    </div>
                    <div>
                        <label for="departure_time" class="block font-semibold text-gray-700">Heure de départ*</label>
                        <div class="flex items-center">
                            <input type="time" name="departure_time" required
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">Si le départ a lieu aujourd’hui, il doit y avoir au moins 6
                                    heures d’écart pour qu’il soit pris en compte !</span>
                            </div>
                        </div>
                        <small id="departure-time-error" class="text-red-600 mt-2"></small>
                    </div>
                    <div>
                        <label for="arrival_date" class="block font-semibold text-gray-700">Date d'arrivée*</label>
                        <div class="flex items-center">
                            <input type="date" name="arrival_date" required disabled
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1 bg-gray-200">
                            <div class="w-4 ml-2"></div>
                        </div>
                        <small id="arrival-date-error" class="text-red-600 mt-2"></small>
                    </div>
                    <div>
                        <label for="arrival_time" class="block font-semibold text-gray-700">Heure d'arrivée*</label>
                        <div class="flex items-center">
                            <input type="time" name="arrival_time" required disabled
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1 bg-gray-200">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">Un trajet doit durer au minimum 10 minutes.</span>
                            </div>
                        </div>
                        <small id="arrival-time-error" class="text-red-600 mt-2"></small>
                    </div>
                    <div>
                        <label for="max_travel_time" class="block font-semibold text-gray-700">Durée maximale du
                            voyage*</label>
                        <div class="flex items-center">
                            <input type="time" name="max_travel_time" required
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">Estimez la durée maximale de votre trajet en incluant les
                                    imprévus (bouchons, travaux, etc.).</span>
                            </div>
                        </div>
                        <small id="max-travel-time-error" class="text-red-600 mt-2"></small>
                    </div>
                </div>

                <!-- Colonne de droite -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-800 border-b pb-2">Lieu d'arrivée</h4>
                    <div>
                        <label for="arrival_address" class="block font-semibold text-gray-700">Adresse
                            d'arrivée*</label>
                        <div class="flex items-center">
                            <input type="text" name="arrival_address" required maxlength="120"
                                oninput="validateFirstChar(this)"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">Maximum 120 caractères.</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="add_arr_address" class="block font-semibold text-gray-700">Complément
                            d'adresse</label>
                        <div class="flex items-center">
                            <input type="text" name="add_arr_address" maxlength="120"
                                oninput="validateFirstChar(this)"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">Maximum 120 caractères.</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="postal_code_arr" class="block font-semibold text-gray-700">Code postal*</label>
                        <div class="flex items-center">
                            <input type="text" name="postal_code_arr" required maxlength="6"
                                oninput="formatPostalCode(this)"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="tooltip ml-2">
                                <span class="text-gray-500">ⓘ</span>
                                <span class="tooltiptext">5 ou 6 chiffres, un espace autorisé.</span>
                            </div>
                        </div>
                        <small id="postal_code_arr-error" class="text-red-600 mt-2"></small>
                    </div>
                    <div>
                        <label for="city_arr" class="block font-semibold text-gray-700">Ville*</label>
                        <div class="flex items-center">
                            <input type="text" name="city_arr" required maxlength="45"
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
                        <label for="voiture_id" class="block font-semibold text-gray-700">Véhicule*</label>
                        <div class="flex items-center">
                            <select name="voiture_id" id="voiture_id_select" required
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
                        <label for="n_tickets" class="block font-semibold text-gray-700">Nombre de places
                            proposées*</label>
                        <div class="flex items-center">
                            <input type="number" name="n_tickets" id="n_tickets_input" required
                                min="1"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1">
                            <div class="w-4 ml-2"></div>
                        </div>
                        <small id="seats-helper" class="text-slate-500 mt-2"></small>
                    </div>
                    <div>
                        <label for="price" class="block font-semibold text-gray-700">Prix par place*</label>
                        <div class="flex items-center">
                            <div class="relative w-full">
                                <input type="number" name="price" required min="2" step="1"
                                    onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) { event.preventDefault(); }"
                                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm mt-1 pr-16">
                                <span
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">crédits</span>
                            </div>
                            <div class="w-4 ml-2"></div>
                        </div>
                        <small class="text-slate-500 mt-2">Dont 2 crédits de commission automatique.</small>
                    </div>

                </div>
            </div>

            <!-- Message d'erreur -->
            <div id="address-error" class="text-red-600 mb-4 mt-8 text-center" style="display: none;"></div>
            <div id="form-general-error" class="text-red-600 mb-4 text-center" style="display: none;"></div>

            <!-- Footer -->
            <div class="mt-8 flex justify-end space-x-4">
                <button type="button" onclick="closeModal('create-covoit-modal')"
                    class="px-4 py-2 text-sm font-semibold text-white bg-slate-500 rounded-lg hover:bg-slate-600 transition-colors duration-300">Annuler</button>
                <button type="submit"
                    class="px-5 py-2 bg-[#2ecc71] text-white font-semibold rounded-md hover:bg-[#27ae60]">Proposer le
                    trajet</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Le premier caractère doit être une lettre ou un chiffre
    function validateFirstChar(element) {
        const value = element.value;
        if (value.length > 0 && !/^[a-zA-Z0-9]/.test(value.charAt(0))) {
            element.value = value.substring(1);
        }
    }

    // Les villes ne peuvent contenir que des lettres, des espaces et des tirets
    function formatCityName(element) {
        const regex = /[^a-zA-Z\séèàáâæçêëîïôöœùúûüÉÈÀÁÂÆÇÊËÎÏÔÖŒÙÚÛÜ\-]/g;
        element.value = element.value.replace(regex, '');
    }

    // Les codes postaux ne peuvent contenir que des chiffres et un espace (au maximum)
    function formatPostalCode(element) {
        let value = element.value;
        // Garde uniquement les chiffres et le premier espace
        value = value.replace(/[^0-9 ]/g, '');
        const firstSpaceIndex = value.indexOf(' ');
        if (firstSpaceIndex !== -1) {
            // S'il y a un espace, on garde tout ce qui est avant, l'espace lui-même,
            // et tout ce qui est après mais en supprimant les autres espaces.
            element.value = value.substring(0, firstSpaceIndex + 1) + value.substring(firstSpaceIndex + 1).replace(
                / /g, '');
        } else {
            element.value = value;
        }
    }

    // Validation du code postal
    function validatePostalCode(inputId, errorId) {
        const input = document.querySelector(`input[name="${inputId}"]`);
        const errorDiv = document.getElementById(errorId);
        const value = input.value.trim();

        if (value.length === 0) {
            errorDiv.textContent = ''; // Pas d'erreur si le champ est vide (le required s'en chargera)
            return true;
        }

        // Règle : Pas plus d'un espace
        if ((value.match(/ /g) || []).length > 1) {
            errorDiv.textContent = 'Un seul espace est autorisé.';
            return false;
        }

        const digitsOnly = value.replace(/ /g, '');
        // Règle : Doit contenir 5 ou 6 chiffres
        if (digitsOnly.length < 5 || digitsOnly.length > 6) {
            errorDiv.textContent = 'Le code postal doit contenir 5 ou 6 chiffres.';
            return false;
        }

        errorDiv.textContent = ''; // Effacer l'erreur si tout est bon
        return true;
    }

    // Validation de l'heure de départ
    function validateDepartureTime() {
        const departureDateInput = document.querySelector('input[name="departure_date"]');
        const departureTimeInput = document.querySelector('input[name="departure_time"]');
        const departureTimeError = document.getElementById('departure-time-error');
        // Date d'aujourd'hui au format YYYY-MM-DD
        // Note à moi-même: ne plus oublier split('T') car le format ISO renvoyé par .toISOString() est YYYY-MM-DDTHH:MM
        const today = new Date().toISOString().split('T')[0];

        departureTimeError.textContent = '';

        // Si la date de départ est aujourd'hui et que l'heure de départ est renseignée
        if (departureDateInput.value === today && departureTimeInput.value) {
            const now = new Date();
            // Heure actuelle + 6h
            now.setHours(now.getHours() + 6);
            const minTime = now.toTimeString().slice(0, 5);

            // Si l'heure de départ est antérieure à l'heure actuelle + 6h
            if (departureTimeInput.value < minTime) {
                departureTimeError.textContent =
                    `Pour un départ aujourd'hui, l'heure doit être au minimum ${minTime}.`;
                return false; // Invalide
            }
        }
        return true; // Valide
    }

    // Validation de l'heure d'arrivée
    function validateArrivalVsDepartureTime() {
        const departureDate = document.querySelector('input[name="departure_date"]').value;
        const departureTime = document.querySelector('input[name="departure_time"]').value;
        const arrivalDate = document.querySelector('input[name="arrival_date"]').value;
        const arrivalTime = document.querySelector('input[name="arrival_time"]').value;
        const arrivalTimeError = document.getElementById('arrival-time-error');

        arrivalTimeError.textContent = ''; // Reset error message

        // Si la date de départ, l'heure de départ, la date d'arrivée et l'heure d'arrivée sont renseignées ET que la date de départ est égale à la date d'arrivée
        if (departureDate && departureTime && arrivalDate && arrivalTime && departureDate === arrivalDate) {
            const start = new Date(`${departureDate}T${departureTime}`);
            const end = new Date(`${arrivalDate}T${arrivalTime}`);
            const diffInMinutes = (end - start) / (1000 * 60);

            // Règle inventé par moi: "L'heure d'arrivée doit être au moins 10 minutes après le départ."
            // Si la différence est inférieure à 10 minutes
            if (diffInMinutes < 10) {
                arrivalTimeError.textContent = "L'heure d'arrivée doit être au moins 10 minutes après le départ.";
                return false; // Invalide
            }
        }
        return true;
    }

    // Validation de la durée maximale
    function validateMaxTravelTime() {
        const maxTimeInput = document.querySelector('input[name="max_travel_time"]');
        const errorDiv = document.getElementById('max-travel-time-error');
        errorDiv.textContent = ''; // Effacer l'erreur si tout est bon

        const maxTimeValue = maxTimeInput.value;
        if (!maxTimeValue)
            return true; // D'après ce que j'ai compris, il vaut mieux mettre cela pour l'expériance utilisateur... Afin qu'il n'y ai pas d'erreur si le champ est vide.
        // C'est le rôle de required (dans le html) de s'assurer que le champ est rempli.
        // Son rôle ici est de valider le format de la durée MAIS UNIQUEMENT si une durée est entrée...
        // Donc, si elle est vide, il ne fait rien.

        // Split en tableau et conversion en milliseconde car c'ets plus facile pour l'ordi pour faire des comparaisons
        const maxTimeParts = maxTimeValue.split(':');
        const maxDurationMs = (parseInt(maxTimeParts[0]) * 3600000) + (parseInt(maxTimeParts[1]) * 60000);

        // La durée max doit être d'au moins 11 minutes
        if (maxDurationMs < (11 * 60000)) {
            errorDiv.textContent = "La durée maximale du voyage doit être d'au moins 11 minutes.";
            return false;
        }

        // La durée max doit être sup à la durée estimée
        const departureDate = document.querySelector('input[name="departure_date"]').value;
        const departureTime = document.querySelector('input[name="departure_time"]').value;
        const arrivalDate = document.querySelector('input[name="arrival_date"]').value;
        const arrivalTime = document.querySelector('input[name="arrival_time"]').value;

        // Si les dates et heures de départ et d'arrivée sont renseignées
        if (departureDate && departureTime && arrivalDate && arrivalTime) {
            const start = new Date(`${departureDate}T${departureTime}`);
            const end = new Date(`${arrivalDate}T${arrivalTime}`);
            const estimatedDurationMs = end - start;

            // Si la durée max est inférieure à la durée estimée
            if (maxDurationMs <= estimatedDurationMs) {
                errorDiv.textContent = 'La durée maximale doit être supérieure à la durée estimée du trajet.';
                return false;
            }
        }

        return true;
    }

    // Validation globale du formulaire
    function validateCovoitForm() {
        const generalErrorDiv = document.getElementById('form-general-error');
        generalErrorDiv.style.display = 'none'; // C'est pour l'expériance utilisateur
        //Si un utilisateur a déjà essayé de soumettre validateCovoitForm() mais qu'il y a eu des erreurs, on cache le message d'erreur pour sa nouvelle tententative...

        // Exécuter toutes les validations... Le but c'est de les exécuter TOUTES AVANT la soumission du formulaire... Et que toutes renvoient true...
        const isDepPostalValid = validatePostalCode('postal_code_dep', 'postal_code_dep-error');
        const isArrPostalValid = validatePostalCode('postal_code_arr', 'postal_code_arr-error');
        const isDepartureTimeValid = validateDepartureTime();
        const isArrivalTimeValid = validateArrivalVsDepartureTime();
        const isMaxTimeValid = validateMaxTravelTime();

        // Comparaison des adresses de départ et d'arrivée
        const depAddress = document.querySelector('input[name="departure_address"]').value.trim();
        const arrAddress = document.querySelector('input[name="arrival_address"]').value.trim();
        const depCity = document.querySelector('input[name="city_dep"]').value.trim();
        const arrCity = document.querySelector('input[name="city_arr"]').value.trim();
        const depPostal = document.querySelector('input[name="postal_code_dep"]').value.trim();
        const arrPostal = document.querySelector('input[name="postal_code_arr"]').value.trim();
        const addressErrorDiv = document.getElementById('address-error');
        let isAddressDifferent = true;

        if (depAddress === arrAddress && depCity === arrCity && depPostal === arrPostal) {
            addressErrorDiv.textContent = 'L\'adresse de départ et d\'arrivée ne peuvent pas être identiques.';
            addressErrorDiv.style.display = 'block';
            isAddressDifferent = false;
        } else {
            addressErrorDiv.style.display = 'none';
        }

        // Verification final=> On vérifie ici tous les résultats de toutes les validations faites avant
        // Si une a échoué => message d'erreur + soumission impossible
        if (!isDepPostalValid || !isArrPostalValid || !isDepartureTimeValid || !isAddressDifferent || !
            isArrivalTimeValid || !isMaxTimeValid) {
            generalErrorDiv.textContent =
                'Le formulaire contient des erreurs. Veuillez vérifier tous les champs.';
            generalErrorDiv.style.display = 'block';
            return false;
        }


        return true; // Tout est ok!
    }

    // On demande ici au navigateur d'attendre que la page se charge avant d'exécuter son code
    document.addEventListener('DOMContentLoaded', function() {
        // On référence tous ses éléments html:
        const addVehicleForm = document.getElementById('addVehicleForm');
        const addVehicleErrors = document.getElementById('add-vehicle-errors');
        const voitureSelect = document.getElementById('voiture_id_select');
        const seatsInput = document.getElementById('n_tickets_input');
        const seatsHelper = document.getElementById('seats-helper');

        const departureDateInput = document.querySelector('input[name="departure_date"]');
        const departureTimeInput = document.querySelector('input[name="departure_time"]');
        const arrivalDateInput = document.querySelector('input[name="arrival_date"]');
        const arrivalTimeInput = document.querySelector('input[name="arrival_time"]');
        const maxTravelTimeInput = document.querySelector('input[name="max_travel_time"]');

        const arrivalDateError = document.getElementById('arrival-date-error');

        // Et on leur attache des addEventListener() pour que cela génére des actions
        document.querySelector('input[name="postal_code_dep"]').addEventListener('blur', () =>
            validatePostalCode(
                'postal_code_dep', 'postal_code_dep-error'));
        document.querySelector('input[name="postal_code_arr"]').addEventListener('blur', () =>
            validatePostalCode(
                'postal_code_arr', 'postal_code_arr-error'));

        departureTimeInput.addEventListener('change', () => {
            validateDepartureTime();
            updateArrivalTimeState();
        });

        arrivalTimeInput.addEventListener('change', validateArrivalVsDepartureTime);
        maxTravelTimeInput.addEventListener('change', validateMaxTravelTime);


        // Ajout d'un nouveau véhicule sans recharger la page => donc, en AJAX
        // On l'utiliser quand un utilisateur rempli le formulaire pour créer un covoiturage (car il peut à ce moment aussi ajouter un véhicule)
        if (addVehicleForm) {
            addVehicleForm.addEventListener('submit', function(e) {
                e.preventDefault();
                addVehicleErrors.innerHTML = ''; // Efface les anciens messages d'erreur

                const formData = new FormData(
                this); // On récupère toutes les données saisie et on les stocke dans formData

                // On envoie les données au serveur
                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')
                                .value,
                            'Accept': 'application/json' // indique au serveur que le navigateur attend une réponse JSON
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.errors) {
                            // Des erreurs ont été trouvées... On nous les renvoie sous forme de liste
                            let errorHtml = '<ul>';
                            for (const error in data.errors) {
                                errorHtml += `<li>${data.errors[error][0]}</li>`;
                            }
                            errorHtml += '</ul>';
                            addVehicleErrors.innerHTML = errorHtml;
                        } else if (data.success) { // Si il n'y a pas d'erreur =>

                            // Le véhicule est créé et maj de la liste des véhicules
                            const newVoiture = data.voiture;
                            // On prépare une nouvelle <option> à mettre dans la liste déroulante
                            const newOption = new Option(
                                `${newVoiture.brand} ${newVoiture.model} (${newVoiture.immat})`,
                                newVoiture.voiture_id);
                            newOption.dataset.places = newVoiture.n_place;

                            // On prend newOption et on l'insère AVANT optgroup dans le select "voitureSelect"
                            const optgroup = voitureSelect.querySelector('optgroup');
                            voitureSelect.insertBefore(newOption, optgroup);
                            // Ce nouveau véhicule sera automatiquement alors sélectionné
                            voitureSelect.value = newVoiture.voiture_id;
                            // Ce qui aura pour effet de maj le n de places dispo
                            voitureSelect.dispatchEvent(new Event('change'));

                            // On ferme alors la modale d'ajout de véhicule et on retourne sur la modale créa de covoit
                            closeModal('add-vehicle-modal');
                            openModal('create-covoit-modal');
                        }
                    })
                    .catch(error => {
                        // Si problème de connexion (ou autre) => message d'erreur
                        console.error('An error occurred:', error);
                        addVehicleErrors.textContent = 'Une erreur inattendue est survenue.';
                    });
            });
        }

        // Code pour rendre la liste déroulante "voitureSelect" interactive
        if (voitureSelect) {
            voitureSelect.addEventListener('change', function() {
                // Il y a deux possibilités: la première si l'utilisateur créait un nouveau véhicule
                if (this.value === 'add_car') {
                    closeModal('create-covoit-modal');
                    openModal('add-vehicle-modal');
                    this.value = ''; // Réinit la sélection si l'utilisateur annule l'ajout
                } else {
                    // Sinon, si il sélectionne un véhicule existant
                    const selectedOption = this.options[this.selectedIndex];
                    const maxPlaces = selectedOption.dataset.places;
                    // On met à jour le nombre de places dispo (-1 que celui de la capacité de la voiture car c'est la place du chauffeur)
                    if (maxPlaces) {
                        const availablePlaces = parseInt(maxPlaces) - 1;
                        seatsInput.max = availablePlaces > 0 ? availablePlaces : 1;
                        seatsHelper.textContent =
                            `Maximum ${seatsInput.max} places pour ce véhicule.`;
                    } else {
                        // Si le véhicule n'a pas de place (ce qui est impossible normalement), on supprimes tout (la valeur max et le message)
                        seatsInput.removeAttribute('max');
                        seatsHelper.textContent = '';
                    }
                }
            });
        }

        // Guide l'utilisateur pour comprendre comment activer les inputs date d'arrivée et heure d'arrivée
        departureDateInput.addEventListener('change', handleDepartureDateChange);
        arrivalDateInput.addEventListener('change', handleArrivalDateChange);

        arrivalDateInput.parentElement.addEventListener('click', () => {
            if (arrivalDateInput.disabled) {
                arrivalDateError.textContent = 'Veuillez d\'abord choisir une date de départ.';
            }
        });

        arrivalTimeInput.parentElement.addEventListener('click', () => {
            if (arrivalTimeInput.disabled) {
                arrivalTimeError.textContent =
                    'Veuillez d\'abord choisir une date et heure de départ.';
            }
        });


        // Des que l'utilisateur choisi une date de départ =>
        function handleDepartureDateChange() {
            if (this.value) {
                arrivalDateInput.disabled = false; // activation de la date d'arrivée
                arrivalDateInput.classList.remove('bg-gray-200'); // et donc changement de son css
                arrivalDateInput.min = this.value; // La date d'arrivée doit être après la date de départ
                arrivalDateError.textContent =
                ''; // On efface le message d'erreur qui auraient pu être affichés avant
            } else {
                arrivalDateInput.disabled = true;
                arrivalDateInput.classList.add('bg-gray-200');
            }
            validateDepartureTime
        (); // Une fois que la date de départ est choisie, on appelle la fonction qui valide l'heure de départ
            updateArrivalTimeState(); // et la fonction qui met à jour l'état de l'heure d'arrivée
        }

        function handleArrivalDateChange() {
            updateArrivalTimeState();
        } // C'est intermédiaire... Quand la date d'arrivée est choisie, cette fonction appelle updateArrivalTimeState() qui met à jour l'état de l'heure d'arrivée

        // C'est le cœur de la logique pour l'heure d'arrivée
        function updateArrivalTimeState() {
            if (departureDateInput.value && departureTimeInput.value) {
                arrivalTimeInput.disabled = false;
                arrivalTimeInput.classList.remove(
                'bg-gray-200'); // Si la date d'arrivée et l'heure de départ sont choisies, on active l'heure d'arrivée (et donc, on change son css)

                // On vide le message d'erreur si la nouvelle valeur est valide
                if (validateArrivalVsDepartureTime()) {
                    document.getElementById('arrival-time-error').textContent = '';
                }

                // Si la date d'arrivée est la même que la date de départ => l'heure d'arrivée doit être au moins 10 minutes après l'heure de départ
                if (arrivalDateInput.value === departureDateInput.value) {
                    let depTime = departureTimeInput.value.split(':');
                    let depDate = new Date();
                    depDate.setHours(parseInt(depTime[0]), parseInt(depTime[1]), 0);

                    depDate.setMinutes(depDate.getMinutes() + 10);

                    let minHour = String(depDate.getHours()).padStart(2, '0');
                    let minMinute = String(depDate.getMinutes()).padStart(2, '0');

                    arrivalTimeInput.min = `${minHour}:${minMinute}`;
                } else {
                    // Sinon, si la date d'arrivée est différente de la date de départ => il n'y a pas de régle concernant l'heure d'arrivée
                    // En mettant min à '' => on supprime toutes les restrictions
                    arrivalTimeInput.min = '';
                }
            } else {
                // Si la date de départ ou l'heure de départ n'est pas choisie => l'heure d'arrivée est désactivée
                arrivalTimeInput.disabled = true;
                arrivalTimeInput.classList.add('bg-gray-200');
                arrivalTimeInput.value = '';
            }
        }
    });
</script>
