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
            const card = this.closest('.covoiturage-card');
            if (card) {
                populateModalFromData(card.dataset);
                modal.classList.remove('hidden');
            }
        });
    });
}

// Génération des étoiles
function generateStars(rating) {
    let starsHtml = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating - fullStars >= 0.5;
    for (let i = 0; i < fullStars; i++) {
        starsHtml += '<i class="fas fa-star text-yellow-400"></i>';
    }
    if (hasHalfStar) {
        starsHtml += '<i class="fas fa-star-half-alt text-yellow-400"></i>';
    }
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    for (let i = 0; i < emptyStars; i++) {
        starsHtml += '<i class="far fa-star text-gray-300"></i>';
    }
    return starsHtml;
}

// Profil du conducteur
function generateDriverProfileHTML(driver) {
    let photoHTML = '';
    if (driver.driverPhoto) {
        photoHTML = `<img src="${driver.driverPhoto}" alt="Photo de ${driver.driverPseudo}" class="w-16 h-16 rounded-full object-cover border-2 border-green-500">`;
    } else {
        photoHTML = `<div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center border-2 border-green-500">
            <i class="fas fa-user text-gray-600 text-xl"></i>
        </div>`;
    }

    const avgRating = parseFloat(driver.driverRatingAvg);
    const totalRatings = parseInt(driver.driverTotalRatings);
    let ratingHTML = '';
    if (avgRating && totalRatings > 0) {
        ratingHTML = `<div class="flex items-center">
            ${generateStars(avgRating)}
            <span class="ml-2 text-gray-600">(${avgRating.toFixed(1)}/5 sur ${totalRatings} avis)</span>
        </div>`;
    } else {
        ratingHTML = '<span class="text-gray-600">Nouveau conducteur</span>';
    }

    return `<div class="flex items-center space-x-4">
        ${photoHTML}
        <div>
            <h5 class="font-semibold text-gray-800 text-lg">${driver.driverPseudo}</h5>
            ${ratingHTML}
        </div>
    </div>`;
}

// Remplissage de la modale
function populateModalFromData(data) {
    // Informations de base
    document.getElementById('modal-city-dep').textContent = data.cityDep || '';
    document.getElementById('modal-city-arr').textContent = data.cityArr || '';
    document.getElementById('modal-departure-address').textContent = data.departureAddress || '';
    document.getElementById('modal-add-dep-address').textContent = data.addDepAddress || '';
    document.getElementById('modal-postal-code-dep').textContent = data.postalCodeDep || '';
    document.getElementById('modal-arrival-address').textContent = data.arrivalAddress || '';
    document.getElementById('modal-add-arr-address').textContent = data.addArrAddress || '';
    document.getElementById('modal-postal-code-arr').textContent = data.postalCodeArr || '';

    // Dates et heures
    document.getElementById('modal-departure-date').textContent = data.departureDate || '';
    document.getElementById('modal-arrival-date').textContent = data.arrivalDate || '';
    document.getElementById('modal-departure-time').textContent = data.departureTime || '';
    document.getElementById('modal-arrival-time').textContent = data.arrivalTime || '';
    document.getElementById('modal-max-travel-time').textContent = data.maxTravelTime ? formatDuration(data.maxTravelTime) : '';

    // Info du trajet
    const nTickets = data.nTickets || 0;
    document.getElementById('modal-n-tickets').textContent = `${nTickets} place${nTickets > 1 ? 's' : ''}`;
    document.getElementById('modal-price').textContent = data.price || '';

    // Badge écologique
    const ecoBadgeContainer = document.getElementById('modal-eco-travel');
    if (data.eco === 'true') {
        ecoBadgeContainer.innerHTML = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><i class="fas fa-leaf mr-2"></i>Trajet écologique</span>`;
    } else {
        ecoBadgeContainer.innerHTML = '';
    }

    // Info du conducteur
    const driverProfileContainer = document.getElementById('modal-driver-profile');
    if (driverProfileContainer) {
        driverProfileContainer.innerHTML = generateDriverProfileHTML(data);
    }

    // Préférences
    document.getElementById('modal-pref-smoke').textContent = data.prefSmoke || 'Non spécifié';
    document.getElementById('modal-pref-pet').textContent = data.prefPet || 'Non spécifié';
    const prefLibreContainer = document.getElementById('modal-pref-libre-container');
    const prefLibre = document.getElementById('modal-pref-libre');
    if (data.prefLibre) {
        prefLibre.textContent = data.prefLibre;
        prefLibreContainer.classList.remove('hidden');
    } else {
        prefLibreContainer.classList.add('hidden');
    }

    // Info du véhicule
    document.getElementById('modal-immat').textContent = data.immat || 'Non disponible';
    document.getElementById('modal-brand').textContent = data.brand || 'Non disponible';
    document.getElementById('modal-model').textContent = data.model || 'Non disponible';
    document.getElementById('modal-color').textContent = data.color || 'Non disponible';
    document.getElementById('modal-energie').textContent = data.energie || 'Non disponible';

    // Pour gagner en vitesse, j'ai décidé de ne pas récupérer les infos en faisant un appel API...
    // Mais pour les avis, je pensais que faire la même chose serait trop lent... Donc, je pensias devoir faire un appel api...
    // Mais rien ne marche???? Après ce commit, je vais essayer encore... Mais en cas d'échec, je vais faire comme pour les autres infos... En limitant néanmoins le nombre d'avis à afficher...

    // Pour afficher la liste des avis => on récupère le html qui a l'id modal-reviews-list
    const reviewsContainer = document.getElementById('modal-reviews-list');
    // On récupére l'id du conducteur pour avoir ses avis
    const driverId = data.driver_id;
    if (window.fetchAndDisplayReviews) {
        window.fetchAndDisplayReviews(driverId, reviewsContainer);
    }


    // Bouton participer
    updateModalButtonFromData(data);
}

function updateModalButtonFromData(data) {
    const modalParticipateBtn = document.querySelector('.modal-participate-btn-js');
    if (modalParticipateBtn) {
        modalParticipateBtn.textContent = data.buttonText;
        modalParticipateBtn.href = data.buttonRedirect || '#';

        // Pour que le btn de la covoiturage-card soit le même que celui de la modale, je suis obligé de faire cela...
        // Réinit les classes de ce btn + on récupére les bonnes class du btn de la covoiturage-card + on les lui applique + et enfin, par sécu, on s'assure que le btn est visible
        modalParticipateBtn.className = 'btn-participate modal-participate-btn-js hidden px-6 py-2 text-white font-bold rounded transition-colors duration-300';
        const buttonClasses = data.buttonClass.split(' ');
        modalParticipateBtn.classList.add(...buttonClasses);
        modalParticipateBtn.classList.remove('hidden');

        const isParticipateAction = data.canParticipate === 'true';
        const tripId = data.id;

        // Attribuer la redirection appropriée au clic
        // Soit l'utilisateur peut participer => btn vert => on récupére le n de place + ferme la modale + redirige vers la page de confirmation
        // Soit l'utilisateur ne peut pas participer => btn rouge ("Se connecter", "Changer de rôle" ou "Recharger votre crédit") => on ferme la modale + on redirige vers la page de connexion ou le dashboard
        modalParticipateBtn.onclick = function(e) {
            e.preventDefault();
            if (isParticipateAction) {
                const seatsInput = document.getElementById('seats');
                const seats = seatsInput ? seatsInput.value : 1;
                document.getElementById('tripDetailsModal').classList.add('hidden');
                window.location.href = `/covoiturage/${tripId}/confirmation?seats=${seats}`;
            } else {
                window.location.href = data.buttonRedirect;
            }
        };
    }
}

function formatDuration(duration) {
    if (typeof duration === 'string' && duration.includes(':')) {
        const parts = duration.split(':');
        const hours = parseInt(parts[0]);
        const minutes = parseInt(parts[1]);
        let result = '';
        if (hours > 0) result += `${hours}h`;
        if (minutes > 0 || hours === 0) result += ` ${minutes}min`;
        return result.trim();
    } else {
        const totalMinutes = parseInt(duration);
        if (isNaN(totalMinutes)) return '';
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        if (hours > 0) {
            return `${hours}h${minutes > 0 ? ' ' + minutes + 'min' : ''}`;
        } else {
            return `${minutes}min`;
        }
    }
}


