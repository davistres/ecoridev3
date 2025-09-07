// Fonctions globales

window.openModifModal = function(button) {
    const covoiturageId = button.dataset.covoiturageId;
    if (!covoiturageId) {
        alert('Impossible de trouver l\'identifiant du covoiturage.');
        return;
    }

    fetch(`/covoiturages/${covoiturageId}/details`)
        .then(response => {
            if (!response.ok) throw new Error('Erreur réseau.');
            return response.json();
        })
        .then(data => {
            const modal = document.getElementById('modif-covoit-modal');
            if (!modal) return;
            const form = modal.querySelector('#modifCovoitForm');

            form.querySelector('[name="departure_address"]').value = data.departure_address || '';
            form.querySelector('[name="add_dep_address"]').value = data.add_dep_address || '';
            form.querySelector('[name="postal_code_dep"]').value = data.postal_code_dep || '';
            form.querySelector('[name="city_dep"]').value = data.city_dep || '';
            form.querySelector('[name="arrival_address"]').value = data.arrival_address || '';
            form.querySelector('[name="add_arr_address"]').value = data.add_arr_address || '';
            form.querySelector('[name="postal_code_arr"]').value = data.postal_code_arr || '';
            form.querySelector('[name="city_arr"]').value = data.city_arr || '';
            form.querySelector('[name="departure_date"]').value = data.departure_date ? data.departure_date.split('T')[0] : '';
            form.querySelector('[name="departure_time"]').value = data.departure_time ? data.departure_time.substring(0, 5) : '';
            form.querySelector('[name="arrival_date"]').value = data.arrival_date ? data.arrival_date.split('T')[0] : '';
            form.querySelector('[name="arrival_time"]').value = data.arrival_time ? data.arrival_time.substring(0, 5) : '';
            form.querySelector('[name="max_travel_time"]').value = data.max_travel_time ? data.max_travel_time.substring(0, 5) : '';
            form.querySelector('[name="voiture_id"]').value = data.voiture_id || '';
            form.querySelector('[name="n_tickets"]').value = data.n_tickets || '';
            form.querySelector('[name="price"]').value = data.price || '';
            form.querySelector('#covoiturage_id').value = data.covoit_id || '';

            let actionBase = form.dataset.actionBase;
            form.setAttribute('action', actionBase.replace('__COVOITURAGE_ID__', data.covoit_id));

            const arrivalDateInput = form.querySelector('[name="arrival_date"]');
            const arrivalTimeInput = form.querySelector('[name="arrival_time"]');
            arrivalDateInput.disabled = false;
            arrivalDateInput.classList.remove('bg-gray-200');
            arrivalTimeInput.disabled = false;
            arrivalTimeInput.classList.remove('bg-gray-200');

            openModal('modif-covoit-modal');
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue.');
        });
}

window.resetCreateCovoitForm = function() {
    const form = document.getElementById('createCovoitForm');
    if (!form) return;
    form.reset();
    const arrivalDateInput = form.querySelector('[name="arrival_date"]');
    const arrivalTimeInput = form.querySelector('[name="arrival_time"]');
    arrivalDateInput.disabled = true;
    arrivalDateInput.classList.add('bg-gray-200');
    arrivalTimeInput.disabled = true;
    arrivalTimeInput.classList.add('bg-gray-200');
    form.querySelectorAll('[id$="-error"]').forEach(el => el.textContent = '');
    form.querySelectorAll('[id$="_error"]').forEach(el => el.style.display = 'none');
    form.querySelector('#create_duration-warning').classList.add('hidden');
}

window.resetModifCovoitForm = function() {
    const form = document.getElementById('modifCovoitForm');
    if (!form) return;
    form.reset();
    form.querySelectorAll('[id$="-error"]').forEach(el => el.textContent = '');
    form.querySelectorAll('[id$="_error"]').forEach(el => el.style.display = 'none');
    form.querySelector('#modif_duration-warning').classList.add('hidden');
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = false;
    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
}

window.validateCovoitForm = function(form) {
    const prefix = form.id === 'createCovoitForm' ? 'create' : 'modif';
    const generalErrorDiv = form.querySelector(`#${prefix}_form-general-error`);
    const addressErrorDiv = form.querySelector(`#${prefix}_address-error`);
    const durationWarningDiv = form.querySelector(`#${prefix}_duration-warning`);
    generalErrorDiv.style.display = 'none';
    addressErrorDiv.style.display = 'none';

    // Vérif que le véhicule est bien sélectionné
    const vehicleSelect = form.querySelector(`#${prefix}_voiture_id_select`);
    if (!vehicleSelect || !vehicleSelect.value || vehicleSelect.value === 'add_car') {
        generalErrorDiv.textContent = 'Veuillez sélectionner un véhicule.';
        generalErrorDiv.style.display = 'block';
        return false;
    }

    const isDurationValid = durationWarningDiv.classList.contains('hidden');
    const isDepPostalValid = window.covoitFormValidators[prefix].validatePostalCode('postal_code_dep', 'postal_code_dep-error');
    const isArrPostalValid = window.covoitFormValidators[prefix].validatePostalCode('postal_code_arr', 'postal_code_arr-error');
    const isDepartureTimeValid = window.covoitFormValidators[prefix].validateDepartureTime();
    const isArrivalTimeValid = window.covoitFormValidators[prefix].validateArrivalVsDepartureTime();
    const isMaxTimeValid = window.covoitFormValidators[prefix].validateMaxTravelTime();

    // Affiche les erreurs spécifiques
    const errorSelectors = [
        `#${prefix}_postal_code_dep-error`,
        `#${prefix}_postal_code_arr-error`,
        `#${prefix}_departure-time-error`,
        `#${prefix}_arrival-time-error`,
        `#${prefix}_max-travel-time-error`
    ];

    errorSelectors.forEach(selector => {
        const errorDiv = form.querySelector(selector);
        if (errorDiv && errorDiv.textContent.trim()) {
            errorDiv.style.display = 'block';
        }
    });

    const depAddress = form.querySelector('[name="departure_address"]').value.trim();
    const arrAddress = form.querySelector('[name="arrival_address"]').value.trim();
    const depCity = form.querySelector('[name="city_dep"]').value.trim();
    const arrCity = form.querySelector('[name="city_arr"]').value.trim();
    const depPostal = form.querySelector('[name="postal_code_dep"]').value.trim();
    const arrPostal = form.querySelector('[name="postal_code_arr"]').value.trim();
    const depAddComp = form.querySelector('[name="add_dep_address"]').value.trim();
    const arrAddComp = form.querySelector('[name="add_arr_address"]').value.trim();

    let isAddressDifferent = true;
    let needsConfirmation = false;

    // Normaliser les chaînes de caractères (suppr espaces, minuscules)
    const normalize = (str) => str.toLowerCase().replace(/\s+/g, '');

    // Normaliser les codes postaux (suppr espaces)
    const normalizePostal = (postal) => postal.replace(/\s+/g, '');

    // Vérif stricte (identiques à 100%)
    const isStrictlyIdentical = (
        normalize(depAddress) === normalize(arrAddress) &&
        normalize(depCity) === normalize(arrCity) &&
        normalizePostal(depPostal) === normalizePostal(arrPostal) &&
        normalize(depAddComp) === normalize(arrAddComp)
    );

    if (isStrictlyIdentical) {
        addressErrorDiv.textContent = 'L\'adresse de départ et d\'arrivée ne peuvent pas être identiques.';
        addressErrorDiv.style.display = 'block';
        isAddressDifferent = false;
    } else {
        // Verif souple (adresse + code postal)
        const isSimilar = (
            normalize(depAddress) === normalize(arrAddress) &&
            normalizePostal(depPostal) === normalizePostal(arrPostal)
        );

        if (isSimilar && depAddress && arrAddress && depPostal && arrPostal) {
            needsConfirmation = true;
        }
    }

    if (!isDepPostalValid || !isArrPostalValid || !isDepartureTimeValid || !isArrivalTimeValid || !isMaxTimeValid || !isAddressDifferent || !isDurationValid) {
        generalErrorDiv.textContent = 'Le formulaire contient des erreurs. Veuillez vérifier tous les champs.';
        generalErrorDiv.style.display = 'block';
        return false;
    }

    // Si vérif souple = false
    if (needsConfirmation) {
        const confirmed = confirm(
            'Les adresses de départ et d\'arrivée semblent être identiques ou très similaires.\n\n' +
            'Départ: ' + depAddress + ' (' + depPostal + ')\n' +
            'Arrivée: ' + arrAddress + ' (' + arrPostal + ')\n\n' +
            'Souhaitez-vous quand même valider votre saisie ?'
        );

        if (!confirmed) {
            return false;
        }
    }

    return true;
}

window.validateFirstChar = function(element) {
    if (element.value.length > 0 && !/^[a-zA-Z0-9]/.test(element.value.charAt(0))) {
        element.value = element.value.substring(1);
    }
}
window.formatCityName = function(element) {
    element.value = element.value.replace(/[^a-zA-Z\séèàáâæçêëîïôöœùúûüÉÈÀÁÂÆÇÊËÎÏÔÖŒÙÚÛÜ\-]/g, '');
}
window.formatPostalCode = function(element) {
    let value = element.value.replace(/[^0-9 ]/g, '');

    // Nouvelle logique stricte pour le code postal
    let formattedValue = "";

    // Les 2 premiers caractères ne peuvent être que des chiffres
    const p1 = value.substring(0, 2).replace(/[^0-9]/g, '');
    formattedValue += p1;

    // On inspecte la 3ème saisie
    if (value.length > 2) {
        const thirdChar = value.charAt(2);

        if (thirdChar === ' ') {
            // Format avec espace : 12 345 (6 caractères total)
            element.maxLength = 6;
            formattedValue += ' ';

            // Les 3 caractères suivants ne peuvent être que des chiffres
            if (value.length > 3) {
                const p2 = value.substring(3, 6).replace(/[^0-9]/g, '');
                formattedValue += p2;
            }
        } else if (/[0-9]/.test(thirdChar)) {
            // Format sans espace : 12345 (5 caractères total)
            element.maxLength = 5;

            // Les 3 caractères suivants ne peuvent être que des chiffres
            const p2 = value.substring(2, 5).replace(/[^0-9]/g, '');
            formattedValue += p2;
        }
        // Si le 3ème caractère n'est ni un espace ni un chiffre, on s'arrête
    }

    element.value = formattedValue;
}

// Autres fonctions (non globales)

document.addEventListener('DOMContentLoaded', function() {
    window.covoitFormValidators = {};

    function setupFormValidation(form, prefix) {
        if (!form) return;

        const validators = {};

        validators.validatePostalCode = (inputId, errorId) => {
            const input = form.querySelector(`[name="${inputId}"]`);
            const errorDiv = form.querySelector(`#${prefix}_${errorId}`);
            const value = input.value.trim();
            if (!value) { errorDiv.textContent = ''; return true; }
            if ((value.match(/ /g) || []).length > 1) { errorDiv.textContent = 'Un seul espace est autorisé.'; return false; }
            const digitsOnly = value.replace(/ /g, '');
            if (digitsOnly.length < 5 || digitsOnly.length > 6) { errorDiv.textContent = 'Le code postal doit contenir 5 ou 6 chiffres.'; return false; }
            errorDiv.textContent = ''; return true;
        };

        validators.validateDepartureTime = () => {
            const departureDateInput = form.querySelector('[name="departure_date"]');
            const departureTimeInput = form.querySelector('[name="departure_time"]');
            const errorDiv = form.querySelector(`#${prefix}_departure-time-error`);
            const today = new Date().toISOString().split('T')[0];
            errorDiv.textContent = '';
            if (departureDateInput.value === today && departureTimeInput.value) {
                const now = new Date();
                now.setHours(now.getHours() + 6);
                const minTime = now.toTimeString().slice(0, 5);
                if (departureTimeInput.value < minTime) {
                    errorDiv.textContent = `Pour un départ aujourd'hui, l'heure doit être au minimum ${minTime}.`;
                    return false;
                }
            }
            return true;
        };

        validators.validateArrivalVsDepartureTime = () => {
            const departureDate = form.querySelector('[name="departure_date"]').value;
            const departureTime = form.querySelector('[name="departure_time"]').value;
            const arrivalDate = form.querySelector('[name="arrival_date"]').value;
            const arrivalTime = form.querySelector('[name="arrival_time"]').value;
            const errorDiv = form.querySelector(`#${prefix}_arrival-time-error`);
            errorDiv.textContent = '';
            if (departureDate && departureTime && arrivalDate && arrivalTime && departureDate === arrivalDate) {
                const start = new Date(`${departureDate}T${departureTime}`);
                const end = new Date(`${arrivalDate}T${arrivalTime}`);
                if ((end - start) / (1000 * 60) < 10) {
                    errorDiv.textContent = "L'heure d'arrivée doit être au moins 10 minutes après le départ.";
                    return false;
                }
            }
            return true;
        };

        validators.validateMaxTravelTime = () => {
            const durationWarningDiv = form.querySelector(`#${prefix}_duration-warning`);
            const errorDiv = form.querySelector(`#${prefix}_max-travel-time-error`);
            if (!durationWarningDiv.classList.contains('hidden')) {
                errorDiv.textContent = '';
                return true;
            }

            const maxTimeInput = form.querySelector('[name="max_travel_time"]');
            errorDiv.textContent = '';
            const maxTimeValue = maxTimeInput.value;
            if (!maxTimeValue) return true;
            const [hours, minutes] = maxTimeValue.split(':');
            const maxDurationMs = (parseInt(hours) * 3600000) + (parseInt(minutes) * 60000);
            if (maxDurationMs < (11 * 60000)) {
                errorDiv.textContent = "La durée maximale doit être d'au moins 11 minutes.";
                return false;
            }
            const departureDate = form.querySelector('[name="departure_date"]').value;
            const departureTime = form.querySelector('[name="departure_time"]').value;
            const arrivalDate = form.querySelector('[name="arrival_date"]').value;
            const arrivalTime = form.querySelector('[name="arrival_time"]').value;
            if (departureDate && departureTime && arrivalDate && arrivalTime) {
                const start = new Date(`${departureDate}T${departureTime}`);
                const end = new Date(`${arrivalDate}T${arrivalTime}`);
                if (maxDurationMs <= (end - start)) {
                    errorDiv.textContent = 'La durée maximale doit être supérieure à la durée estimée du trajet.';
                    return false;
                }
            }
            return true;
        };

        window.covoitFormValidators[prefix] = validators;

        const departureDateInput = form.querySelector('[name="departure_date"]');
        const departureTimeInput = form.querySelector('[name="departure_time"]');
        const arrivalDateInput = form.querySelector('[name="arrival_date"]');
        const arrivalTimeInput = form.querySelector('[name="arrival_time"]');
        const voitureSelect = form.querySelector(`#${prefix}_voiture_id_select`);
        const seatsInput = form.querySelector(`#${prefix}_n_tickets_input`);
        const seatsHelper = form.querySelector(`#${prefix}_seats-helper`);
        const durationWarningDiv = form.querySelector(`#${prefix}_duration-warning`);
        const submitButton = form.querySelector('button[type="submit"]');

        function checkTripDuration() {
            const depDate = departureDateInput.value;
            const depTime = departureTimeInput.value;
            const arrDate = arrivalDateInput.value;
            const arrTime = arrivalTimeInput.value;

            if (depDate && depTime && arrDate && arrTime) {
                const start = new Date(`${depDate}T${depTime}`);
                const end = new Date(`${arrDate}T${arrTime}`);
                const diffInMinutes = (end - start) / (1000 * 60);

                if (diffInMinutes > 1439) { // 23H59mn
                    durationWarningDiv.classList.remove('hidden');
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    durationWarningDiv.classList.add('hidden');
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                durationWarningDiv.classList.add('hidden');
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            validators.validateMaxTravelTime();
        }

        function updateArrivalTimeState() {
            if (departureDateInput.value && departureTimeInput.value) {
                arrivalTimeInput.disabled = false;
                arrivalTimeInput.classList.remove('bg-gray-200');

                // Si les dates sont identiques, l'heure d'arrivée doit être au moins 10 minutes après le départ
                if (arrivalDateInput.value === departureDateInput.value) {
                    let [hours, minutes] = departureTimeInput.value.split(':');
                    let minArrivalTime = new Date();
                    minArrivalTime.setHours(parseInt(hours), parseInt(minutes));
                    minArrivalTime.setMinutes(minArrivalTime.getMinutes() + 10);
                    arrivalTimeInput.min = minArrivalTime.toTimeString().slice(0, 5);
                } else {
                    // Si les dates sont différentes, pas de limite pour l'heure
                    arrivalTimeInput.removeAttribute('min');
                }
            } else {
                arrivalTimeInput.disabled = true;
                arrivalTimeInput.classList.add('bg-gray-200');
                arrivalTimeInput.removeAttribute('min');
            }
            checkTripDuration();
        }

        departureDateInput.addEventListener('change', function() {
            if (this.value) {
                arrivalDateInput.disabled = false;
                arrivalDateInput.classList.remove('bg-gray-200');
                arrivalDateInput.min = this.value;
                if (arrivalDateInput.value < this.value) arrivalDateInput.value = this.value;
            } else {
                arrivalDateInput.disabled = true;
                arrivalDateInput.classList.add('bg-gray-200');
            }
            updateArrivalTimeState();
        });

        departureTimeInput.addEventListener('change', updateArrivalTimeState);
        arrivalDateInput.addEventListener('change', updateArrivalTimeState);
        arrivalTimeInput.addEventListener('change', checkTripDuration);

        if (voitureSelect) {
            voitureSelect.addEventListener('change', function() {
                if (this.value === 'add_car') {
                    // Quelle modale ouvrir?
                    const currentModalId = form.closest('.fixed').id;

                    if (currentModalId === 'create-covoit-modal') {
                        // Depuis create-covoit-modal, ouvrir addcovoit-addvehicle-modal
                        openAddCovoitVehicleModal();
                    } else {
                        // Depuis modif-covoit-modal, ouvrir add-vehicle-modal normal
                        closeModal(currentModalId);
                        openModal('add-vehicle-modal');
                    }
                    this.value = '';
                } else {
                    const selectedOption = this.options[this.selectedIndex];
                    const maxPlaces = selectedOption.dataset.places;
                    if (maxPlaces) {
                        const availablePlaces = parseInt(maxPlaces, 10) - 1;
                        seatsInput.max = availablePlaces > 0 ? availablePlaces : 1;
                        seatsHelper.textContent = `Maximum ${seatsInput.max} places.`;
                    } else {
                        seatsInput.removeAttribute('max');
                        seatsHelper.textContent = '';
                    }
                }
            });
        }
    }

    setupFormValidation(document.getElementById('createCovoitForm'), 'create');
    setupFormValidation(document.getElementById('modifCovoitForm'), 'modif');

    // Validation du covoit (réinit window.temporaryVehicleId)
    const createCovoitForm = document.getElementById('createCovoitForm');
    if (createCovoitForm) {
        createCovoitForm.addEventListener('submit', function(event) {
            if (!validateCovoitForm(this)) {
                event.preventDefault(); // Empêche la soumission si la validation échoue
            } else {
                // Si valide, le véhicule n'est plus temporaire
                window.temporaryVehicleId = null;
            }
        });
    }

    // Réinit pour create-covoit-modal
    window.resetCreateCovoitModal = function() {
        const createForm = document.getElementById('createCovoitForm');
        if (!createForm) return;

        // Suppr le véhicule temporaire si y en a un
        if (window.temporaryVehicleId) {
            fetch(`/voitures/${window.temporaryVehicleId}/temporary`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    console.log('Véhicule temporaire supprimé.');
                    // Retire l'option du select
                    const vehicleSelect = document.getElementById('create_voiture_id_select');
                    const optionToRemove = vehicleSelect.querySelector(`option[value="${window.temporaryVehicleId}"]`);
                    if (optionToRemove) {
                        vehicleSelect.removeChild(optionToRemove);
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression du véhicule temporaire:', error);
            })
            .finally(() => {
                // Très important : réinit la variable !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                window.temporaryVehicleId = null;
            });
        }

        // Réinit le formulaire
        createForm.reset();

        // Réinit les champs disabled
        const arrivalDateInput = createForm.querySelector('[name="arrival_date"]');
        const arrivalTimeInput = createForm.querySelector('[name="arrival_time"]');

        if (arrivalDateInput) {
            arrivalDateInput.disabled = true;
            arrivalDateInput.classList.add('bg-gray-200');
        }

        if (arrivalTimeInput) {
            arrivalTimeInput.disabled = true;
            arrivalTimeInput.classList.add('bg-gray-200');
        }

        // Efface les messages
        const errorElements = createForm.querySelectorAll('[id$="-error"]');
        errorElements.forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });

        const warningElements = createForm.querySelectorAll('[id$="-warning"]');
        warningElements.forEach(element => {
            element.classList.add('hidden');
        });

        // Le bouton de soumission doit se réactiver
        const submitButton = createForm.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    };


});
