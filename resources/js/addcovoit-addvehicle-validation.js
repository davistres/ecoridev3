// Validation pour addcovoit-addvehicle-modal
document.addEventListener('DOMContentLoaded', function() {
    const addCovoitVehicleForm = document.getElementById('addCovoitVehicleForm');

    if (addCovoitVehicleForm) {
        // Stocker les données du formulaire create-covoit
        let savedCovoitFormData = {};

        // Même logique que add-vehicle-modal => valadation des entrées
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

        // Appliquer les validations
        const modelInput = addCovoitVehicleForm.querySelector('#addcovoit-model');
        const brandInput = addCovoitVehicleForm.querySelector('#addcovoit-brand');
        const colorInput = addCovoitVehicleForm.querySelector('#addcovoit-color');
        const immatInput = addCovoitVehicleForm.querySelector('#addcovoit-immat');

        if (modelInput) modelInput.addEventListener('input', validateFirstCharIsAlphaNum);
        if (brandInput) brandInput.addEventListener('input', validateBrandAndColor);
        if (colorInput) colorInput.addEventListener('input', validateBrandAndColor);
        if (immatInput) immatInput.addEventListener('input', validateImmat);

        // Validation pour la soumission
        addCovoitVehicleForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const immatValue = immatInput.value.toUpperCase();
            const immatError = addCovoitVehicleForm.querySelector('#addcovoit-immat_error');
            const validImmatRegex = /^[A-Z]{2}[-]?[0-9]{3}[-]?[A-Z]{2}$/;

            if (!validImmatRegex.test(immatValue)) {
                immatError.textContent = 'Le format de l\'immatriculation est invalide.';
                return;
            } else {
                immatError.textContent = '';
            }

            // Soumettre le véhicule via AJAX
            submitVehicleForCovoit();
        });

        // Sauvegarder les données de create-covoit
        window.saveCovoitFormData = function() {
            const createForm = document.getElementById('createCovoitForm');
            if (!createForm) return;

            savedCovoitFormData = {};
            const formData = new FormData(createForm);
            for (let [key, value] of formData.entries()) {
                // Ne pas sauvegarder la valeur "add_car" du select véhicule
                // car ce n'est pas une donnée (une voiture) mais un déclencheur pour ouvrire la modale addcovoit-addvehicle-modal
                // voiture_id (ou create_voiture_id_select) est l'id du select contenant l'option "add_car"
                if (key === 'voiture_id' && value === 'add_car') {
                    continue;
                }
                savedCovoitFormData[key] = value;
            }
        };

        // Restaure les données de create-covoit
        window.restoreCovoitFormData = function() {
            const createForm = document.getElementById('createCovoitForm');
            if (!createForm || !savedCovoitFormData) return;

            for (let [key, value] of Object.entries(savedCovoitFormData)) {
                const input = createForm.querySelector(`[name="${key}"]`);
                if (input && value) {
                    // La valeur du select véhicule est déjà sur le nouveau véhicule => pas la peind e la restaurer
                    if (key === 'voiture_id') {
                        continue;
                    }
                    input.value = value;

                    // Si on sort de addcovoit-addvehicle-modal => donc on retourne OBLIGATOIREMENT sur create-covoit-modal
                    // Donc, si on avait déjà entré une date et une heure de départ, ça va remettre les valeurs
                    // Problème: des champs dépendent d'autres champs (ex: jour et heures d'arrivée)!
                    // SOLUTION: on simule un clic avec l'événement 'change' pour forcer la mise à jour du reste du formulaire
                    if (key === 'departure_date' || key === 'departure_time') {
                        input.dispatchEvent(new Event('change'));
                    }
                }
            }
        };

        // Pour soumettre un véhicule
        function submitVehicleForCovoit() {
            const formData = new FormData(addCovoitVehicleForm);
            // On met ici toutes les données du formulaire create-covoit dans un objet (formData) contruit en sivant le modéle de "FormData" prêt à être envoyé

            // Pour parler au serveur sans recharcher la page (en AJAX donc) => fetch
            fetch('/voitures', {
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
                // Si la requête n'est pas OK
                if (!response.ok) {
                    if (response.status === 422) { // 422 => erreur de validation larravel
                        return response.json().then(errorData => {
                            // On construit un message d'erreur en lisant le contenu de l'erreur
                            const errors = errorData.errors || {};
                            let errorMessage = 'Erreurs de validation:\n';
                            for (const [field, messages] of Object.entries(errors)) {
                                errorMessage += `- ${field}: ${messages.join(', ')}
`;
                            }
                            // et on lance une erreur pour être capturée par le .catch()
                            throw new Error(errorMessage);
                        });
                    }
                    // Pour toutes autres erreurs, on déclenche ceci
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                // Si on arrive là, c'est que la requête a réussi (en Json)
                // On passe alors au .then()
                return response.json();
            })
            .then(data => {
                if (data.success) { // Si tout est ok => data contient la réponse du serveur (sucess: true, voiture: {données du véhicule}...)
                    // On sauve l'id du véhicule temporaire dans une variable globale
                    window.temporaryVehicleId = data.voiture.voiture_id;

                    // on trouve le select
                    const vehicleSelect = document.getElementById('create_voiture_id_select');

                    // Dans le select, on crée une nouvelle option
                    const newOption = document.createElement('option');
                    newOption.value = data.voiture.voiture_id;
                    newOption.textContent = `${data.voiture.brand} ${data.voiture.model} (${data.voiture.immat})`;
                    newOption.setAttribute('data-places', data.voiture.n_place);

                    // On insère cette option AVANT l'optgroup
                    const addCarOptgroup = vehicleSelect.querySelector('optgroup');
                    vehicleSelect.insertBefore(newOption, addCarOptgroup);

                    // On sélectionne cette option
                    vehicleSelect.value = data.voiture.voiture_id;

                    // On déclenche l'événement change pour simuler un clic et mettre à jour les champs dépendants (le nombre de places)
                    vehicleSelect.dispatchEvent(new Event('change', { bubbles: true }));

                    // on ferme la modale pour ajouter un véhicule
                    document.getElementById('addcovoit-addvehicle-modal').classList.add('hidden');
                    addCovoitVehicleForm.reset();
                    const immatError = addCovoitVehicleForm.querySelector('#addcovoit-immat_error');
                    if (immatError) immatError.textContent = '';


                    // On réouvre la modale de création de covoi sans la réinit
                    openModal('create-covoit-modal', false);

                    // On restaure les données du formulaire create-covoit (que l'utilisateur avait déjà entré)
                    restoreCovoitFormData();

                } else {
                    alert('Erreur lors de l\'ajout du véhicule');
                }
            })
            // Filet de sécu classique pour attraper une erreur (de n'importe où) et l'afficher dans la console
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'ajout du véhicule: ' + error.message);
            });
        }

        // Ouvrir addcovoit-addvehicle-modal (la modale d'ajout de véhicule) depuis create-covoit-modal (la modale de covoiturage)
        window.openAddCovoitVehicleModal = function() {
            // Sauve les données du formulaire create-covoit
            saveCovoitFormData();

            closeModal('create-covoit-modal');

            document.getElementById('addcovoit-addvehicle-modal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };
    }
});
