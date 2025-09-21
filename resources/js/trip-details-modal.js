// Modale au clic sur le btn "Détails" des covoiturage-card
document.addEventListener("DOMContentLoaded", function() {
    initTripDetailsModal();
});

function initTripDetailsModal() {
    const detailsButtons = document.querySelectorAll('.btn-details');
    const modal = document.getElementById('tripDetailsModal');

    if (!detailsButtons.length || !modal) return;

    // Fermeture de la modale
    const closeButtons = modal.querySelectorAll('.modal-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    });

    // Fermeture si on clique hors de la modale
    modal.addEventListener('click', function(event) {
        if (event.target === modal || event.target.classList.contains('modal-overlay')) {
            modal.classList.add('hidden');
        }
    });

    // Btn "Détails"
    detailsButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            let tripId;

            // id du covoit
            if (this.hasAttribute('data-id')) {
                tripId = this.getAttribute('data-id');
            } else {
                const tripUrl = this.getAttribute('href');
                if (tripUrl) {
                    tripId = tripUrl.split('/').pop();
                } else {
                    console.error('Impossible de récupérer l\'ID du covoiturage');
                    return;
                }
            }

            // Récupére l'état du btn (dans la div booking-buttons) depuis de la covoiturage-card correspondante
            const cardContainer = this.closest('.covoiturage-card');
            let buttonData = null;

            if (cardContainer) {
                const bookingButtons = cardContainer.querySelector('.booking-buttons');
                if (bookingButtons) {
                    const buttons = bookingButtons.querySelectorAll('a');
                    const participateBtn = buttons[1]; // Index 1 = deuxième bouton (le premier c'est celui du détail, le deuxième c'est celui que l'on veut = celui qui change d'état en fonction de la situation)

                    if (participateBtn) {
                        buttonData = {
                            button_text: participateBtn.textContent.trim(),
                            redirect_to: participateBtn.getAttribute('href'),
                            button_classes: participateBtn.className
                        };
                    }
                }
            }

            console.log('ID du covoiturage:', tripId);
            console.log('Données du bouton:', buttonData);
            fetchTripDetails(tripId, buttonData);
        });
    });
}

// Récupére les infos du covoit
function fetchTripDetails(tripId, buttonData = null) {
    const modal = document.getElementById('tripDetailsModal');
    if (!modal) return;

    console.log('Récupération des détails du covoiturage:', tripId);
    console.log('Données du bouton reçues:', buttonData);

    // Affiche de la modale avec l'indicateur de chargement
    modal.classList.remove('hidden');
    document.getElementById('modal-loading').classList.remove('hidden');
    document.getElementById('modal-content').classList.add('hidden');
    document.getElementById('modal-button-loading').classList.remove('hidden');
    document.querySelector('.modal-participate-btn-js').classList.add('hidden');

    const apiUrl = `/api/trips/${tripId}/details`;
    console.log('URL de l\'API:', apiUrl);

    // Récupére les infos du covoit
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur lors de la récupération des détails: ${response.status}`);
            }
            return response.json();
        })
        .then(tripData => {
            // Rempli la modale
            populateModalWithData(tripData);

            // Masquer l'indicateur de chargement et afficher le contenu
            document.getElementById('modal-loading').classList.add('hidden');
            document.getElementById('modal-content').classList.remove('hidden');
            document.getElementById('modal-button-loading').classList.add('hidden');

            // On utilise les données du btn de la covoiturage-card au lieu de l'API
            if (buttonData) {
                console.log('Utilisation des données du bouton de la carte:', buttonData);
                updateModalButtonFromCard(buttonData, tripId);
            } else {
                // Normalement, ça ne devrait pas arriver!!!!!
                // Mais au cas où... Si on ne trouve aucune donnée sur le btn de la card, alors on cache le btn d'action.
                console.warn('Aucune donnée de bouton de carte trouvée. Le bouton d\'action de la modale sera masqué.');
                document.querySelector('.modal-participate-btn-js').classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des détails:', error);
            document.getElementById('modal-loading').classList.add('hidden');
            document.getElementById('modal-content').classList.remove('hidden');
            document.getElementById('modal-reviews-list').innerHTML = '<div class="text-center text-red-500 py-8">Erreur lors du chargement des détails</div>';
            document.getElementById('modal-button-loading').classList.add('hidden');

            // Même en cas d'erreur, on utilisera les données du btn de la covoiturage-card
            if (buttonData) {
                updateModalButtonFromCard(buttonData, tripId);
            } else {
                document.querySelector('.modal-participate-btn-js').classList.add('hidden');
            }
        });
}

// Info dans la modale
function populateModalWithData(data) {
    console.log('Données reçues:', data);

    // Informations de base
    document.getElementById('modal-city-dep').textContent = data.city_dep || '';
    document.getElementById('modal-city-arr').textContent = data.city_arr || '';
    document.getElementById('modal-departure-address').textContent = data.departure_address || '';
    document.getElementById('modal-add-dep-address').textContent = data.add_dep_address || '';
    document.getElementById('modal-postal-code-dep').textContent = data.postal_code_dep || '';
    document.getElementById('modal-arrival-address').textContent = data.arrival_address || '';
    document.getElementById('modal-add-arr-address').textContent = data.add_arr_address || '';
    document.getElementById('modal-postal-code-arr').textContent = data.postal_code_arr || '';

    // Dates et heures
    try {
        const departureDate = data.departure_date ? new Date(data.departure_date) : new Date();
        const arrivalDate = data.arrival_date ? new Date(data.arrival_date) : departureDate;

        document.getElementById('modal-departure-date').textContent = formatDate(departureDate);
        document.getElementById('modal-arrival-date').textContent = formatDate(arrivalDate);

        document.getElementById('modal-departure-time').textContent = data.departure_time ? data.departure_time.substring(0, 5) : '';
        document.getElementById('modal-arrival-time').textContent = data.arrival_time ? data.arrival_time.substring(0, 5) : '';

        document.getElementById('modal-max-travel-time').textContent = data.max_travel_time ? formatDuration(data.max_travel_time) : '';
    } catch (error) {
        console.error('Erreur lors du traitement des dates et heures:', error);
    }

    // Info du trajet
    const placesRestantes = data.places_restantes || data.n_tickets || 0;
    document.getElementById('modal-n-tickets').textContent = `${placesRestantes} place${placesRestantes > 1 ? 's' : ''}`;
    document.getElementById('modal-price').textContent = data.price || '';

    // Badge écologique
    const ecoBadgeContainer = document.getElementById('modal-eco-travel');
    if (data.eco_travel) {
        ecoBadgeContainer.innerHTML = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><i class="fas fa-leaf mr-2"></i>Trajet écologique</span>`;
    } else {
        ecoBadgeContainer.innerHTML = ''; // On n'affiche rien
    }

    // Info du conducteur
    if (data.driver) {
        document.getElementById('modal-driver-pseudo').textContent = data.driver.name || '';

        // Photo
        const driverPhoto = document.getElementById('modal-driver-photo');
        if (data.driver.photo && data.driver.phototype) {
            driverPhoto.innerHTML = `<img src="data:${data.driver.phototype};base64,${data.driver.photo}" alt="${data.driver.name}" class="w-full h-full object-cover rounded-full">`;
        } else {
            driverPhoto.innerHTML = '<i class="fas fa-user text-2xl text-gray-500"></i>';
        }

        // Note
        const driverRating = document.getElementById('modal-driver-rating');
        const driverStars = document.getElementById('modal-driver-stars');

        if (data.driver.average_rating && data.driver.total_ratings > 0) {
            driverRating.textContent = `${parseFloat(data.driver.average_rating).toFixed(1)}/5`;
            driverStars.innerHTML = generateStars(data.driver.average_rating);
        } else {
            driverRating.textContent = '';
            driverStars.textContent = 'Nouveau conducteur';
        }

        // Préférences
        document.getElementById('modal-pref-smoke').textContent = data.driver.pref_smoke || 'Non spécifié';
        document.getElementById('modal-pref-pet').textContent = data.driver.pref_pet || 'Non spécifié';

        const prefLibreContainer = document.getElementById('modal-pref-libre-container');
        const prefLibre = document.getElementById('modal-pref-libre');

        if (data.driver.pref_libre) {
            prefLibre.textContent = data.driver.pref_libre;
            prefLibreContainer.classList.remove('hidden');
        } else {
            prefLibreContainer.classList.add('hidden');
        }
    }

    // Info du véhicule
    if (data.voiture) {
        document.getElementById('modal-immat').textContent = data.voiture.immat || 'Non disponible';
        document.getElementById('modal-brand').textContent = data.voiture.brand || 'Non disponible';
        document.getElementById('modal-model').textContent = data.voiture.model || 'Non disponible';
        document.getElementById('modal-color').textContent = data.voiture.color || 'Non disponible';
        document.getElementById('modal-energie').textContent = data.voiture.energie || 'Non disponible';
    } else {
        document.getElementById('modal-immat').textContent = 'Non disponible';
        document.getElementById('modal-brand').textContent = 'Non disponible';
        document.getElementById('modal-model').textContent = 'Non disponible';
        document.getElementById('modal-color').textContent = 'Non disponible';
        document.getElementById('modal-energie').textContent = 'Non disponible';
    }

    // Avis
    populateReviews(data.reviews || []);

    // Bouton participer
    const participateBtn = document.querySelector('.modal-participate-btn-js');
    const tripId = data.covoit_id;
    if (tripId) {
        participateBtn.href = `/covoiturage/${tripId}/participate`;
    }
}

// Avis
function populateReviews(reviews) {
    const reviewsList = document.getElementById('modal-reviews-list');

    if (!reviewsList) {
        console.error('Conteneur modal-reviews-list non trouvé');
        return;
    }

    reviewsList.innerHTML = '';

    if (reviews && reviews.length > 0) {
        console.log(`${reviews.length} avis trouvés`);

        reviews.forEach((review, index) => {
            const reviewCard = createReviewCard(review);
            reviewsList.appendChild(reviewCard);
        });
    } else {
        console.log('Aucun avis trouvé');
        reviewsList.innerHTML = '<div class="no-reviews">Aucun avis pour ce conducteur</div>';
    }
}

// Card des avis
function createReviewCard(review) {
    const card = document.createElement('div');
    card.className = 'review-card';

    const header = document.createElement('div');
    header.className = 'review-header';

    const author = document.createElement('span');
    author.className = 'review-author';
    author.textContent = review.utilisateur ? review.utilisateur.name : 'Anonyme';

    const date = document.createElement('span');
    date.className = 'review-date';
    date.textContent = review.date ? formatDate(new Date(review.date)) : '';

    header.appendChild(author);
    header.appendChild(date);

    const rating = document.createElement('div');
    rating.className = 'review-rating';
    rating.innerHTML = generateStars(review.note);

    const content = document.createElement('div');
    content.className = 'review-content';
    content.textContent = review.review || '';

    card.appendChild(header);
    card.appendChild(rating);
    card.appendChild(content);

    return card;
}

// Format date
function formatDate(date) {
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Format durée
function formatDuration(duration) {
    // HH:MM:SS ou minutes
    if (typeof duration === 'string' && duration.includes(':')) {
        const parts = duration.split(':');
        const hours = parseInt(parts[0]);
        const minutes = parseInt(parts[1]);

        let result = '';
        if (hours > 0) {
            result += `${hours}h`;
        }
        if (minutes > 0 || hours === 0) {
            result += `${minutes}min`;
        }
        return result;
    } else {
        // Si c'est un nombre de minutes
        const totalMinutes = parseInt(duration);
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;

        if (hours > 0) {
            return `${hours}h${minutes > 0 ? ' ' + minutes + 'min' : ''}`;
        } else {
            return `${minutes}min`;
        }
    }
}

// Génère les étoiles
function generateStars(rating) {
    let starsHtml = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating - fullStars >= 0.5;

    // Étoiles pleines
    for (let i = 0; i < fullStars; i++) {
        starsHtml += '<span class="star active">★</span>';
    }

    // Étoile à moitié
    if (hasHalfStar) {
        starsHtml += '<span class="star half-active">★</span>';
    }

    // Étoiles vides
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    for (let i = 0; i < emptyStars; i++) {
        starsHtml += '<span class="star empty">☆</span>';
    }

    return starsHtml;
}

// Copie les infos du btn de la card dans le btn de la modale
function updateModalButtonFromCard(buttonData, tripId) {
    const modalParticipateBtn = document.querySelector('.modal-participate-btn-js');
    if (modalParticipateBtn && buttonData) {
        // On copie le texte et le lien depuis la card
        modalParticipateBtn.textContent = buttonData.button_text;
        modalParticipateBtn.href = buttonData.redirect_to || '#';

        // On copie aussi TOUTES les class du btn de la card => pour la même apparence
        modalParticipateBtn.className = buttonData.button_classes;

        // + ajout de class spécifique qui pourraient manquer (comme le padding)
        // + on s'assure qu'il est visible.
        modalParticipateBtn.classList.add('px-6', 'py-2', 'modal-participate-btn-js');
        modalParticipateBtn.classList.remove('hidden');

        // Le btn affiche "Participer" ou une autre action?
        const isParticipateAction = buttonData.button_text === 'Participer';

        // Suppr les anciens eventlisteners et ajouter le nouveau
        modalParticipateBtn.onclick = function(e) {
            e.preventDefault();
            if (isParticipateAction) {
                // On récupère le n place depuis la recherche (ou 1 par défaut)
                const seatsInput = document.getElementById('seats');
                const seats = seatsInput ? seatsInput.value : 1;

                // Fermeture de la modale
                document.getElementById('tripDetailsModal').classList.add('hidden');
                // Redirige vers la page de confirmation avec le n place
                window.location.href = `/covoiturage/${tripId}/confirmation?seats=${seats}`;
            } else {
                // Pour les autres btn (Se connecter, Changer de rôle et Recharger votre crédit)
                window.location.href = buttonData.redirect_to;
            }
        };

        console.log('Bouton modal mis à jour:', {
            text: buttonData.button_text,
            href: buttonData.redirect_to,
            isParticipate: isParticipateAction
        });
    }
}
