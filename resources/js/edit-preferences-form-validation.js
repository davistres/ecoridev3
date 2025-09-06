// Validation pour edit-preferences-modal
document.addEventListener('DOMContentLoaded', function() {
    const editPreferencesForm = document.getElementById('editPreferencesForm');

    if (editPreferencesForm) {
        const prefLibreInput = editPreferencesForm.querySelector('#edit-pref_libre');

        // 1ère saisie => que une lettre ou un chiffre
        const validateFirstCharIsAlphaNum = (event) => {
            const input = event.target;
            let value = input.value;
            while (value.length > 0 && !/^[a-zA-Z0-9]/.test(value)) {
                value = value.substring(1);
            }
            input.value = value;
        };

        if(prefLibreInput) {
            prefLibreInput.addEventListener('input', validateFirstCharIsAlphaNum);
        }

        // Réinit de la modale
        window.resetEditPreferencesModal = function() {
            if (editPreferencesForm) {
                // Réinit les champs du formulaire
                editPreferencesForm.reset();

                // Remettre les valeurs de l'utilisateur
                const prefSmokeInputs = editPreferencesForm.querySelectorAll('input[name="pref_smoke"]');
                const prefPetInputs = editPreferencesForm.querySelectorAll('input[name="pref_pet"]');
                const prefLibreTextarea = editPreferencesForm.querySelector('#edit-pref_libre');

                // Réinit les radio selon le choix de l'utilisateur
                prefSmokeInputs.forEach(input => {
                    if (input.hasAttribute('checked')) {
                        input.checked = true;
                    }
                });

                prefPetInputs.forEach(input => {
                    if (input.hasAttribute('checked')) {
                        input.checked = true;
                    }
                });

                // Réinit le textarea avec la valeur de l'utilisateur
                if (prefLibreTextarea && prefLibreTextarea.defaultValue) {
                    prefLibreTextarea.value = prefLibreTextarea.defaultValue;
                }
            }
        }
    }
});
