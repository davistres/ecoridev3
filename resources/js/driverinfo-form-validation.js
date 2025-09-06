document.addEventListener('DOMContentLoaded', function () {
    const driverInfoForm = document.getElementById('driverInfoForm');

    if (driverInfoForm) {
        const prefLibreInput = driverInfoForm.querySelector('#pref_libre');
        const modelInput = driverInfoForm.querySelector('#model');
        const brandInput = driverInfoForm.querySelector('#brand');
        const colorInput = driverInfoForm.querySelector('#color');
        const immatInput = driverInfoForm.querySelector('#immat');
        const photoInput = driverInfoForm.querySelector('#driver_profile_photo');

        // La première saisie ne doit être que une lettre ou un chiffre
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
                // La 1ère saisie ne doit être qu'une lettre
                while (value.length > 0 && !/^[a-zA-Z]/.test(value)) {
                    value = value.substring(1);
                }

                // Que pour avvoir des lettres ou des -
                if (value.length > 1) {
                    let firstChar = value.charAt(0);
                    let rest = value.substring(1);
                    // Trop complexe!!!! A retenir!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    // replace = on ne veut pas les caractères entre []... On les remplace par ''= du vide = donc on les supprime!
                    // Sauf que, en mettant ça: ^=non => on inverse la situation en précisant que l'on ne voudra pas des caractères qui ne sont pas comme ceux []... Donc, du coup, on "replace" les caractères qui seront pas indiqué dans le [] (=> qui ne  sont pas des lettres ou des -)
                    // \p{L} = toutes les lettres de tous les alphabets (C'est bien plus complet que [a-zA-Z])
                    // - = -
                    // g = pour global (pour être sûr que la recherche se fera sur toutes les saisies)
                    // u = unicode (je n'ai pas compri mais c'est OBLIGATOIRE pour que \p{L} fonctionne)
                    rest = rest.replace(/[^\p{L}-]/gu, '');
                    value = firstChar + rest;
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

        // Taille de la photo
        // On définie la constante avec un objet event, car event= c'est ce que l'utilisateur va déclencher lorsqu'il va sélectionner une photo
        const validatePhotoSize = (event) => {
            const photoError = driverInfoForm.querySelector('#profile_photo_error'); // On cible l'élément qui affichera l'erreur avec son ID
            const file = event.target.files[0]; // On cible le fichier sélectionné

            if (file) { // Ce IF c'est ce qui me manquait pour ne plus avoir d'erreur si on ouvre la modale sans sélectionner de photo!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! A ne pas oublier!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                const maxSize = 2 * 1024 * 1024; // Taille max autorisé 2MB
                if (file.size > maxSize) { // LOGIQUE PRINCIPALE : on compare la taille du fichier avec la taille max autorisé
                    photoError.textContent = 'Le fichier est trop volumineux. La taille maximale est de 2 Mo.'; // On insère ce message dans l'ID ciblé plus haut
                    event.target.value = ''; // On efface la photo sélectionnée
                } else {
                    photoError.textContent = ''; // On efface le message d'erreur si la photo est valide
                }
            }
        };

        if(prefLibreInput) {
            prefLibreInput.addEventListener('input', validateFirstCharIsAlphaNum);
        }

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

        if(photoInput) {
            photoInput.addEventListener('change', validatePhotoSize);
        }

        driverInfoForm.addEventListener('submit', function(event) {
            const immatInput = driverInfoForm.querySelector('#immat');
            const immatError = driverInfoForm.querySelector('#immat_error');
            const immatValue = immatInput.value.toUpperCase();

            const validImmatRegex = /^(?:[A-Z]{2}-[0-9]{3}-[A-Z]{2}|[A-Z]{2}[0-9]{3}[A-Z]{2})$/;

            if (!validImmatRegex.test(immatValue)) {
                event.preventDefault(); // Stop la soumission du formulaire avec ce message d'erreur
                immatError.textContent = 'Le format de l\'immatriculation est invalide.';
            } else {
                immatError.textContent = ''; // Efface le message d'erreur (si yen a un) si la saisie est valide
            }
        });

        window.resetDriverInfoModal = function() {
            if (driverInfoForm) {
                driverInfoForm.reset();

                const photoError = driverInfoForm.querySelector('#profile_photo_error');
                if (photoError) {
                    photoError.textContent = '';
                }

                const immatError = driverInfoForm.querySelector('#immat_error');
                if (immatError) {
                    immatError.textContent = '';
                }

                const immatInput = driverInfoForm.querySelector('#immat');
                if (immatInput) {
                    immatInput.maxLength = 9;
                }
            }
        }
    }
});
