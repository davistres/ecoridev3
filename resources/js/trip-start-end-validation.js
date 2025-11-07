// Validation pour les btn "DÉMARRER" ET "VOUS ÊTES ARRIVÉ"
// Ce code est commenté pour permette des tests rapides... Mais ensuite, je dois le décommenter!!!!!!!!!!!!!!!!!!!!!!!!!!!
// A NE PAS OUBLIER!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Init des modales de conf
document.addEventListener('DOMContentLoaded', function() {
    // Modale de démarrage anticipé
    const earlyStartModal = document.getElementById('confirm-early-start-modal');
    if (earlyStartModal) {
        const closeButtons = earlyStartModal.querySelectorAll('.modal-close');
        const overlay = earlyStartModal.querySelector('.modal-overlay');

        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => closeModal('confirm-early-start-modal'));
        });

        if (overlay) {
            overlay.addEventListener('click', () => closeModal('confirm-early-start-modal'));
        }
    }

    // Modale de fin de covoiturage
    const tripEndModal = document.getElementById('confirm-trip-end-modal');
    if (tripEndModal) {
        const closeButtons = tripEndModal.querySelectorAll('.modal-close');
        const overlay = tripEndModal.querySelector('.modal-overlay');

        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => closeModal('confirm-trip-end-modal'));
        });

        if (overlay) {
            overlay.addEventListener('click', () => closeModal('confirm-trip-end-modal'));
        }
    }
});

// Valider le démarrage d'un covoit
function validateTripStart(card, startBtn, endBtn, modifierBtn) {
    /* A décommenter!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    const departureDate = card.dataset.departureDate;
    const departureTime = card.dataset.departureTime;

    if (!departureDate || !departureTime) {
        console.error('Données de départ manquantes');
        return false;
    }

    const now = new Date();
    const scheduledDeparture = new Date(departureDate + ' ' + departureTime);
    const timeDiff = scheduledDeparture - now;
    const minutesDiff = Math.floor(timeDiff / 1000 / 60);

    // Plus d'1 heure en avance : refus
    if (minutesDiff > 60) {
        alert('Impossible de démarrer un covoiturage programmé avec plus d\'une heure d\'avance.\n\nHeure de départ prévue : ' + departureTime + '\nVeuillez attendre encore ' + Math.floor(minutesDiff / 60) + ' heure(s) et ' + (minutesDiff % 60) + ' minute(s).');
        return false;
    }

    // Entre 15 et 60 minutes en avance : demander confirmation
    if (minutesDiff > 15 && minutesDiff <= 60) {
        const scheduledTimeSpan = document.getElementById('early-start-scheduled-time');
        if (scheduledTimeSpan) {
            scheduledTimeSpan.textContent = departureTime;
        }

        openModal('confirm-early-start-modal');

        const confirmBtn = document.getElementById('confirm-early-start-btn');
        if (confirmBtn) {
            confirmBtn.onclick = function() {
                closeModal('confirm-early-start-modal');
                performTripStart(card, startBtn, endBtn, modifierBtn);
            };
        }

        return false;
    }

    // Moins de 15 minutes en avance ou après l'heure : OK
    return true;

    FIN DU CODE À DÉCOMMENTER ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
    return true;
}

// Démarrage du covoit
function performTripStart(card, startBtn, endBtn, modifierBtn) {
    modifierBtn.disabled = true;
    modifierBtn.classList.add('opacity-50', 'cursor-not-allowed');
    startBtn.classList.add('hidden');
    endBtn.classList.remove('hidden');
    card.dataset.tripStarted = 'true';

    // Event pour informer le système de notif
    const event = new CustomEvent('trip-started', {
        detail: { tripId: card.dataset.covoiturageId }
    });
    document.dispatchEvent(event);
}

// Valider la fin d'un covoit
function validateTripEnd(card) {
   /* A décommenter!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    const departureDate = card.dataset.departureDate;
    const departureTime = card.dataset.departureTime;
    const arrivalDate = card.dataset.arrivalDate;
    const arrivalTime = card.dataset.arrivalTime;

    if (!departureDate || !departureTime || !arrivalDate || !arrivalTime) {
        console.error('Données de trajet manquantes');
        return false;
    }

    const now = new Date();
    const scheduledDeparture = new Date(departureDate + ' ' + departureTime);
    const scheduledArrival = new Date(arrivalDate + ' ' + arrivalTime);

    // Calcule de la durée du trajet en mn
    const tripDuration = (scheduledArrival - scheduledDeparture) / 1000 / 60;

// Trajet > 30 minutes
    if (tripDuration > 30) {
        const halfTripTime = new Date(scheduledDeparture.getTime() + (tripDuration / 2) * 60 * 1000);

        // Si on n'a pas encore atteint la moitié du temps de trajet
        if (now < halfTripTime) {
            const remainingMinutes = Math.ceil((halfTripTime - now) / 1000 / 60);
            const hours = Math.floor(remainingMinutes / 60);
            const minutes = remainingMinutes % 60;

            let timeMessage = '';
            if (hours > 0) {
                timeMessage = hours + ' heure(s)';
                if (minutes > 0) {
                    timeMessage += ' et ' + minutes + ' minute(s)';
                }
            } else {
                timeMessage = minutes + ' minute(s)';
            }

            alert('Déjà arrivé ?!?\n\nVous ne pouvez pas encore valider votre trajet car l\'heure d\'arrivée prévue (' + arrivalTime + ') est encore assez éloignée.\n\nVeuillez attendre encore ' + timeMessage + ' avant de pouvoir confirmer la fin du covoiturage.');
            return false;
        }
    }

    // Si le trajet fait moins de 30 minutes OU si on a dépassé la moitié du temps : OK
    return true;

    FIN DU CODE À DÉCOMMENTER ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
    return true;
}

// Affiche la modale de conf à la fin d'un covoit
function showTripEndConfirmation(card, callback) {
    const cityDep = card.dataset.cityDep;
    const cityArr = card.dataset.cityArr;
    const routeSpan = document.getElementById('trip-end-route');

    if (routeSpan) {
        routeSpan.textContent = cityDep + ' → ' + cityArr;
    }

    openModal('confirm-trip-end-modal');

    const confirmBtn = document.getElementById('confirm-trip-end-btn');
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            closeModal('confirm-trip-end-modal');
            if (callback) {
                callback();
            }
        };
    }
}

// Export des fonctions pour utilisation globale
window.validateTripStart = validateTripStart;
window.performTripStart = performTripStart;
window.validateTripEnd = validateTripEnd;
window.showTripEndConfirmation = showTripEndConfirmation;

