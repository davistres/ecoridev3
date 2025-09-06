// Validation pour add-vehicle-modal et edit-vehicle-modal
document.addEventListener('DOMContentLoaded', function() {
    const addVehicleForm = document.getElementById('addVehicleForm');
    const editVehicleForm = document.getElementById('editVehicleForm');

    // 1èresaisie => que une lettre ou un chiffre
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

        if (value.length > 0) {
            // 1ère saisie => que une lettre
            while (value.length > 0 && !/^[a-zA-Z]/.test(value)) {
                value = value.substring(1);
            }

            // Après (la 1ère lettre), on autorise les lettres, chiffres, espaces et tirets
            if (value.length > 1) {
                value = value.charAt(0) + value.substring(1).replace(/[^a-zA-Z0-9\s\-]/g, '');
            }
        }

        input.value = value;
    };

    // Formatage de la plaque d'immat
    // On récupére la saisie et on la met en majuscule
    const validateImmat = (event) => {
        const input = event.target;
        let value = input.value.toUpperCase();

        // On supprime tous les caractères qui ne sont pas des lettres, des chiffres ou des tirets
        value = value.replace(/[^A-Z0-9-]/g, '');

        // Plutôt que de modifier, on créait une nouvelle chaine de caractères vide
        let formattedValue = "";

        // Partie 1: Les 2 premiers caractères ne doivent être que des lettres
        const p1 = value.substring(0, 2).replace(/[^A-Z]/g, '');
        formattedValue += p1;

        // On inspecte la 3éme saisie pour déterminer la suite
        if (value.length > 2) {
            const thirdChar = value.charAt(2);

            // Si c'est un tiret, il y aura donc 9 caractères avec ce format: AA-123-BB... Ici, on est dans H1 (hypothèse 1)
            if (thirdChar === '-') {
                formattedValue += '-';

                // Partie 2 de H1: Les 3 caractères suivants ne doivent être que des chiffres
                const p2 = value.substring(3, 6).replace(/[^0-9]/g, '');
                formattedValue += p2;

                // On inspecte le 7éme caractère pour déterminer la suite
                if (value.length > 6) {
                    const seventhChar = value.charAt(6);
                    // Partie 3 de H1: Si c'est un tiret, il y aura donc 9 caractères avec l'obligation de respecter ce format: AA-123-BB
                    if (seventhChar === '-') {
                        formattedValue += '-';
                        const p3 = value.substring(7, 9).replace(/[^A-Z]/g, '');
                        formattedValue += p3;
                    } else {
                        // Si ce n'est pas un tiret, on arrête là
                    }
                }
            } else if (/[0-9]/.test(thirdChar)) {
                // Si la saisie du 3éme caractère n'est pas un tiret, alors ça doit être un chiffre.
                // il y aura 7 caractères avec ce format: AA123BB
                // Ici, on est dans H2 (hypothèse 2)
                // RPartie 2 de H2: Les 3 caractères suivants ne doivent être que des chiffres
                const p2_part1 = value.substring(2, 5).replace(/[^0-9]/g, '');
                formattedValue += p2_part1;

                // Partie 3 de H2: Les 2 caractères suivants ne doivent être que des lettres
                if (value.length > 5) {
                    const p3 = value.substring(5, 7).replace(/[^A-Z]/g, '');
                    formattedValue += p3;
                }
            } else {
                // Si le 3éme caractère n'est ni un tiret, ni un chiffre, on arrête là
            }
        }

        input.value = formattedValue;
    };

    // Config les validations
    function setupVehicleValidation(form, prefix) {
        if (!form) return;

        const modelInput = form.querySelector(`#${prefix}-model`);
        const brandInput = form.querySelector(`#${prefix}-brand`);
        const colorInput = form.querySelector(`#${prefix}-color`);
        const immatInput = form.querySelector(`#${prefix}-immat`);

        if(modelInput) {
            modelInput.addEventListener('input', validateFirstCharIsAlphaNum);
        }

        if(brandInput) {
            brandInput.addEventListener('input', validateBrandAndColor);
        }

        if(colorInput) {
            colorInput.addEventListener('input', validateBrandAndColor);
        }

        if(immatInput) {
            immatInput.addEventListener('input', validateImmat);
        }

        // Validation lors du submit
        form.addEventListener('submit', function(event) {
            const immatInput = form.querySelector(`#${prefix}-immat`);
            const immatError = form.querySelector(`#${prefix}-immat_error`);
            const immatValue = immatInput.value.toUpperCase();

            const validImmatRegex = /^(?:[A-Z]{2}-[0-9]{3}-[A-Z]{2}|[A-Z]{2}[0-9]{3}[A-Z]{2})$/;

            if (!validImmatRegex.test(immatValue)) {
                event.preventDefault(); // Stop la soumission avec ce message d'erreur
                if (immatError) {
                    immatError.textContent = 'Le format de l\'immatriculation est invalide.';
                }
            } else {
                // Efface les messages d'erreur (si yen a un) si la saisie est valide
                if (immatError) {
                    immatError.textContent = '';
                }
            }
        });
    }

    // Config les 2 formulaires
    setupVehicleValidation(addVehicleForm, 'add');
    setupVehicleValidation(editVehicleForm, 'edit');

    // Réinit pour add-vehicle-modal
    window.resetAddVehicleModal = function() {
        if (addVehicleForm) {
            // Réinit tous les champs du formulaire
            addVehicleForm.reset();

            // Efface les messages d'erreur
            const errorElements = addVehicleForm.querySelectorAll('[id$="_error"]');
            errorElements.forEach(element => {
                element.textContent = '';
            });
        }
    }

    // Réinit pour edit-vehicle-modal
    window.resetEditVehicleModal = function() {
        if (editVehicleForm) {
            // Pareil => réinit tous les champs du formulaire
            editVehicleForm.reset();

            // Efface tous les messages d'erreur
            const errorElements = editVehicleForm.querySelectorAll('[id$="_error"]');
            errorElements.forEach(element => {
                element.textContent = '';
            });
        }
    }
});
