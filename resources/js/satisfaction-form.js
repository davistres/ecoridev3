document.addEventListener('DOMContentLoaded', function () {
    const satisfactionModal = document.getElementById('satisfaction-form-modal');
    const satisfactionForm = document.getElementById('satisfactionForm');
    const errorsContainer = document.getElementById('satisfaction-errors');
    const errorsList = errorsContainer?.querySelector('ul');

    const feelingRadios = document.querySelectorAll('input[name="feeling"]');
    const commentSection = document.getElementById('comment-section');
    const commentTextarea = document.getElementById('comment');
    const commentRequiredIndicator = document.getElementById('comment-required-indicator');

    const reviewTextarea = document.getElementById('review');
    const noteSection = document.getElementById('note-section');
    const noteInput = document.getElementById('note');
    const noteRequiredIndicator = document.getElementById('note-required-indicator');
    const stars = document.querySelectorAll('#star-rating i');

    let selectedRating = 0;

    // Nouvelle logique de validation
    // handleFirstCharValidation sert à valider le premier caractère des champs de commentaires et avis
    function handleFirstCharValidation(event) {
        const textarea = event.target;
        let value = textarea.value;
        const errorMsgContainer = textarea.nextElementSibling; // Vise l'élément <small>
        // Regex incluant les lettres, chiffres et accents spécifiés. Le flag 'u' est pour le support Unicode.
        // Regex étant une séquence de caractères qui spécifie un modèle de correspondance dans un texte
        const regex = /^[a-zA-Z0-9éèàêç]/u;

        if (value.length > 0 && !regex.test(value)) {
            // Si le 1er caractère est invalide
            textarea.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            textarea.classList.remove('focus:border-green-500', 'focus:ring-green-500');
            if (errorMsgContainer && !errorMsgContainer.dataset.originalText) {
                errorMsgContainer.dataset.originalText = errorMsgContainer.textContent;
                errorMsgContainer.textContent = 'Le premier caractère doit être une lettre, un chiffre ou un accent (é, è, à, ê, ç).';
                errorMsgContainer.classList.add('text-red-500');
            }
            // Suppr immédiatement le caractère invalide
            textarea.value = value.substring(1);
        } else {
            // Si le 1ercaractère est valide ou si le champ est vide
            textarea.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            textarea.classList.add('focus:border-green-500', 'focus:ring-green-500');
            if (errorMsgContainer && errorMsgContainer.dataset.originalText) {
                errorMsgContainer.textContent = errorMsgContainer.dataset.originalText;
                errorMsgContainer.classList.remove('text-red-500');
                delete errorMsgContainer.dataset.originalText;
            }
        }
    }

    commentTextarea.addEventListener('input', handleFirstCharValidation);
    reviewTextarea.addEventListener('input', handleFirstCharValidation);


    feelingRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.value === '0') {
                commentTextarea.setAttribute('required', 'required');
                commentRequiredIndicator.classList.remove('hidden');
            } else {
                commentTextarea.removeAttribute('required');
                commentRequiredIndicator.classList.add('hidden');
            }
        });
    });

    reviewTextarea.addEventListener('input', function () {
        if (this.value.trim() !== '') {
            noteRequiredIndicator.classList.remove('hidden');
        } else {
            noteRequiredIndicator.classList.add('hidden');
            selectedRating = 0;
            noteInput.value = '';
            updateStars(0);
        }
    });

    stars.forEach(star => {
        star.addEventListener('click', function () {
            selectedRating = parseInt(this.getAttribute('data-rating'));
            noteInput.value = selectedRating;
            updateStars(selectedRating);
        });

        star.addEventListener('mouseenter', function () {
            const rating = parseInt(this.getAttribute('data-rating'));
            updateStars(rating, true);
        });
    });

    const starRatingContainer = document.getElementById('star-rating');
    starRatingContainer.addEventListener('mouseleave', function () {
        updateStars(selectedRating);
    });

    function updateStars(rating, isHover = false) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('far', 'text-gray-300');
                star.classList.add('fas', 'text-yellow-400');
            } else {
                star.classList.remove('fas', 'text-yellow-400');
                star.classList.add('far', 'text-gray-300');
            }
        });
    }

    if (satisfactionForm) {
        satisfactionForm.addEventListener('submit', function (e) {
            e.preventDefault();

            errorsContainer.classList.add('hidden');
            errorsList.innerHTML = '';

            let customErrors = [];
            const formData = new FormData(satisfactionForm);
            const data = {
                satisfaction_id: formData.get('satisfaction_id'),
                covoit_id: formData.get('covoit_id'),
                feeling: formData.get('feeling') === '1' ? 1 : 0,
                comment: formData.get('comment') || null,
                review: formData.get('review') || null,
                note: formData.get('note') || null,
                user_nickname: formData.get('user_nickname'), // Honeypot
            };

            if (data.feeling === 0 && !data.comment) {
                customErrors.push('Le commentaire est obligatoire si vous n\'êtes pas satisfait.');
            }

            if (data.review && !data.note) {
                customErrors.push('La note est obligatoire si vous laissez un avis.');
            }

            if (customErrors.length > 0) {
                showErrors(customErrors);
                return;
            }

            const submitUrl = satisfactionForm.getAttribute('data-submit-url');
            const csrfToken = document.querySelector('input[name="_token"]').value;

            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (response.status === 422) {
                        return response.json().then(result => {
                            if (result.errors) {
                                const errorMessages = Object.values(result.errors).flat();
                                showErrors(errorMessages);
                            }
                            throw new Error('Validation failed');
                        });
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        closeModal('satisfaction-form-modal');
                        if (window.showSuccessNotification) {
                            window.showSuccessNotification(result.message || 'Merci pour votre retour !');
                        }
                        satisfactionForm.reset();
                        selectedRating = 0;
                        updateStars(0);
                        commentRequiredIndicator.classList.add('hidden');
                        noteRequiredIndicator.classList.add('hidden');

                        const satisfactionBtn = document.querySelector(`[data-satisfaction-id="${data.satisfaction_id}"]`);
                        if (satisfactionBtn) {
                            const parentContainer = satisfactionBtn.closest('.flex.flex-col.sm\\:flex-row.gap-0');
                            if (parentContainer) {
                                parentContainer.remove();
                            } else {
                                satisfactionBtn.remove();
                            }
                        }

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else if (result.message) {
                        showErrors([result.message]);
                    }
                })
                .catch(error => {
                    if (error.message !== 'Validation failed') {
                        console.error('Erreur:', error);
                        showErrors(['Une erreur réseau est survenue. Veuillez réessayer.']);
                    }
                });
        });
    }

    function showErrors(errors) {
        errorsList.innerHTML = '';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorsList.appendChild(li);
        });
        errorsContainer.classList.remove('hidden');
    }

    window.openSatisfactionForm = function (satisfactionId, covoitId, driverName, tripDate, tripRoute) {
        document.getElementById('satisfaction_id').value = satisfactionId;
        document.getElementById('covoit_id').value = covoitId;
        document.getElementById('driver-name-display').textContent = driverName;
        document.getElementById('trip-date-display').textContent = tripDate;
        document.getElementById('trip-route-display').textContent = tripRoute;

        satisfactionForm.reset();
        // Réinit les messages d'erreur potentiels lors de l'ouverture
        handleFirstCharValidation({ target: commentTextarea });
        handleFirstCharValidation({ target: reviewTextarea });
        selectedRating = 0;
        updateStars(0);
        errorsContainer.classList.add('hidden');
        errorsList.innerHTML = '';
        commentRequiredIndicator.classList.add('hidden');
        noteRequiredIndicator.classList.add('hidden');

        openModal('satisfaction-form-modal');
    };
});

