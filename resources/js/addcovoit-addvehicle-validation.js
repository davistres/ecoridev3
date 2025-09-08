// VALIDATION ET DE SOUMISSION POUR TOUTES LES MODALES DE VÉHICULE
document.addEventListener('DOMContentLoaded', function() {

    const setupVehicleModal = (formId, prefix) => {
        const form = document.getElementById(formId);
        if (!form) return;

        // Validation côté client
        const modelInput = form.querySelector(`#${prefix}-model`);
        const brandInput = form.querySelector(`#${prefix}-brand`);
        const colorInput = form.querySelector(`#${prefix}-color`);
        const immatInput = form.querySelector(`#${prefix}-immat`);

        const validateFirstCharIsAlphaNum = (event) => {
            const input = event.target;
            let value = input.value;
            while (value.length > 0 && !/^[a-zA-Z0-9]/.test(value)) {
                value = value.substring(1);
            }
            input.value = value;
        };

        const validateBrandAndColor = (event) => {
            const input = event.target;
            let value = input.value;

            while (value.length > 0 && !/^[a-zA-Z]/.test(value)) {
                value = value.substring(1);
            }

            value = value.replace(/[^a-zA-Z\séèàáâæçêëîïôöœùúûüÉÈÀÁÂÆÇÊËÎÏÔÖŒÙÚÛÜ\-]/g, '');
            input.value = value;
        };

        const validateImmat = (event) => {
            const input = event.target;
            let value = input.value.toUpperCase();
            value = value.replace(/[^A-Z0-9-]/g, '');

            let formattedValue = "";
            const p1 = value.substring(0, 2).replace(/[^A-Z]/g, '');
            formattedValue += p1;

            if (value.length > 2) {
                const thirdChar = value.charAt(2);

                if (thirdChar === '-') {
                    formattedValue += '-';
                    const p2 = value.substring(3, 6).replace(/[^0-9]/g, '');
                    formattedValue += p2;

                    if (value.length > 6) {
                        const seventhChar = value.charAt(6);
                        if (seventhChar === '-') {
                            formattedValue += '-';
                            const p3 = value.substring(7, 9).replace(/[^A-Z]/g, '');
                            formattedValue += p3;
                        }
                    }
                } else if (/[0-9]/.test(thirdChar)) {
                    const p2_part1 = value.substring(2, 5).replace(/[^0-9]/g, '');
                    formattedValue += p2_part1;

                    if (value.length > 5) {
                        const p3 = value.substring(5, 7).replace(/[^A-Z]/g, '');
                        formattedValue += p3;
                    }
                }
            }

            input.value = formattedValue;
        };

        if (modelInput) modelInput.addEventListener('input', validateFirstCharIsAlphaNum);
        if (brandInput) brandInput.addEventListener('input', validateBrandAndColor);
        if (colorInput) colorInput.addEventListener('input', validateBrandAndColor);
        if (immatInput) immatInput.addEventListener('input', validateImmat);

        // Soumission formulaire
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const immatValue = immatInput.value.toUpperCase();
            const immatError = form.querySelector(`#${prefix}-immat-error`);
            const generalErrorContainer = form.querySelector(`#${prefix}-vehicle-errors`);
            const submitButton = form.querySelector('button[type="submit"]');
            const validImmatRegex = /^[A-Z]{2}[-]?[0-9]{3}[-]?[A-Z]{2}$/;

            // Réinit des erreurs
            if(immatError) immatError.innerHTML = '';
            if(generalErrorContainer) generalErrorContainer.classList.add('hidden');

            // Validation du format
            if (!validImmatRegex.test(immatValue)) {
                if (immatError) immatError.innerHTML = 'Le format de l\'immatriculation est invalide.';
                return;
            }

            submitButton.disabled = true;
            submitButton.classList.add('opacity-50');

            const formData = new FormData(form);
            const method = form.querySelector('input[name="_method"]')?.value || 'POST';

            fetch(form.action, {
                method: 'POST',
                body: formData, // On retrouve ici le contenu de notre envoi (= les données du formulaire)
                headers: {
                    // 'X-CSRF-TOKEN' => jeton de sécu pour prouver que la requete provient bien du site
                    // 'Accept': 'application/json' => on attend une réponse en JSON
                    // 'X-Requested-With': 'XMLHttpRequest' => on dit que c'est une requête AJAX
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.status === 422) {
                    return response.json().then(data => Promise.reject(data));
                }
                if (!response.ok) {
                    throw new Error('Une erreur serveur est survenue.');
                }
                return response.json();
            })
            .then(data => {
                // Si c'est la modale "addcovoit", on gère la logique spécifique
                if (formId === 'addCovoitVehicleForm' && data.success) {
                    handleSuccessForAddCovoit(data.voiture);
                } else {
                    // Pour les autres modales, on recharge simplement la page
                    location.reload();
                }
            })
            .catch(error => {
                // Si c'est une erreur de validation de Laravel
                if (error && error.errors) {
                    // Ou afficher l'erreur pour le champ 'immat'?
                    const immatErrorDiv = form.querySelector(`#${prefix}-immat_error`);
                    if (error.errors.immat && immatErrorDiv) {
                        immatErrorDiv.innerHTML = error.errors.immat[0];
                    } else {
                        // Si c'est autre erreur de validation => on l'affiche dans le conteneur principal
                        const generalErrorDiv = form.querySelector(`#${prefix}-vehicle-errors ul`);
                        if (generalErrorDiv) {
                            generalErrorDiv.innerHTML = '';
                            Object.values(error.errors).flat().forEach(msg => {
                                const li = document.createElement('li');
                                li.innerHTML = msg;
                                generalErrorDiv.appendChild(li);
                            });
                            generalErrorDiv.parentElement.classList.remove('hidden');
                        }
                    }
                } else {
                    // Erreur = alerte
                    alert(error.message || 'Une erreur inattendue est survenue.');
                }
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50');
            });
        });
    };

    // Logique addcovoit-addvehicle
    let savedCovoitFormData = {};

    const handleSuccessForAddCovoit = (voiture) => {
        window.temporaryVehicleId = voiture.voiture_id;
        const vehicleSelect = document.getElementById('create_voiture_id_select');
        const newOption = document.createElement('option');
        newOption.value = voiture.voiture_id;
        newOption.textContent = `${voiture.brand} ${voiture.model} (${voiture.immat})`;
        newOption.setAttribute('data-places', voiture.n_place);
        const addCarOptgroup = vehicleSelect.querySelector('optgroup');
        vehicleSelect.insertBefore(newOption, addCarOptgroup);
        vehicleSelect.value = voiture.voiture_id;
        vehicleSelect.dispatchEvent(new Event('change', { bubbles: true }));
        closeModal('addcovoit-addvehicle-modal');
        document.getElementById('addCovoitVehicleForm').reset();
        openModal('create-covoit-modal', false);
        restoreCovoitFormData();
    };

    window.saveCovoitFormData = function() {
        const createForm = document.getElementById('createCovoitForm');
        if (!createForm) return;
        savedCovoitFormData = {};
        const formData = new FormData(createForm);
        for (let [key, value] of formData.entries()) {
            if (key !== 'voiture_id' || value !== 'add_car') {
                savedCovoitFormData[key] = value;
            }
        }
    };

    window.restoreCovoitFormData = function() {
        const createForm = document.getElementById('createCovoitForm');
        if (!createForm || !savedCovoitFormData) return;
        for (let [key, value] of Object.entries(savedCovoitFormData)) {
            const input = createForm.querySelector(`[name="${key}"]`);
            if (input && value && key !== 'voiture_id') {
                input.value = value;
                if (key === 'departure_date' || key === 'departure_time') {
                    input.dispatchEvent(new Event('change'));
                }
            }
        }
    };

    window.openAddCovoitVehicleModal = function() {
        saveCovoitFormData();
        closeModal('create-covoit-modal');
        openModal('addcovoit-addvehicle-modal');
    };

    window.closeAddCovoitVehicleModal = function() {
        closeModal('addcovoit-addvehicle-modal');
        if (window.temporaryVehicleId) {
            fetch(`/voitures/${window.temporaryVehicleId}/temporary`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            }).then(() => window.temporaryVehicleId = null);
        }
        openModal('create-covoit-modal', false);
        restoreCovoitFormData();
    };

    setupVehicleModal('addCovoitVehicleForm', 'addcovoit');
    setupVehicleModal('addVehicleForm', 'add');
    setupVehicleModal('editVehicleForm', 'edit');
});
