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

            const formData = new FormData(satisfactionForm);
            const data = {
                satisfaction_id: formData.get('satisfaction_id'),
                feeling: formData.get('feeling') === '1' ? 1 : 0,
                comment: formData.get('comment') || null,
                review: formData.get('review') || null,
                note: formData.get('note') || null,
            };

            if (data.feeling === 0 && !data.comment) {
                showErrors(['Le commentaire est obligatoire si vous n\'êtes pas satisfait.']);
                return;
            }

            if (data.review && !data.note) {
                showErrors(['La note est obligatoire si vous laissez un avis.']);
                return;
            }

            const submitUrl = satisfactionForm.getAttribute('data-submit-url');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
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
                    } else {
                        if (result.errors) {
                            const errorMessages = Object.values(result.errors).flat();
                            showErrors(errorMessages);
                        } else {
                            showErrors([result.message || 'Une erreur est survenue.']);
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showErrors(['Une erreur réseau est survenue. Veuillez réessayer.']);
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
        selectedRating = 0;
        updateStars(0);
        errorsContainer.classList.add('hidden');
        errorsList.innerHTML = '';
        commentRequiredIndicator.classList.add('hidden');
        noteRequiredIndicator.classList.add('hidden');

        openModal('satisfaction-form-modal');
    };
});

