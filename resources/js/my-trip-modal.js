// Modale "Mon covoiturage" pour les covoit proposés par un conducteur
document.addEventListener("DOMContentLoaded", function() {
    initMyTripModal();
});

// Init de la modale "Mon covoiturage"
function initMyTripModal() {
    const myTripDetailsButtons = document.querySelectorAll('.btn-my-trip-details');
    const modal = document.getElementById('myTripModal');

    if (!myTripDetailsButtons.length || !modal) return;

    // Fermeture de la modale
    const closeButtons = modal.querySelectorAll('.modal-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    });

    // Fermeture au clique hors de la modale
    modal.addEventListener('click', function(event) {
        if (event.target === modal || event.target.classList.contains('modal-overlay')) {
            modal.classList.add('hidden');
        }
    });

    // Btn "Détails" dans "Mes covoiturages proposés" du dashboard
    myTripDetailsButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const card = this.closest('.covoiturage-card');
            if (card) {
                populateMyTripModal(card.dataset);
                modal.classList.remove('hidden');
            }
        });
    });
}

// Info de la modale
function populateMyTripModal(data) {
    // Info de base
    document.getElementById('my-trip-city-dep').textContent = data.cityDep || '';
    document.getElementById('my-trip-city-arr').textContent = data.cityArr || '';
    document.getElementById('my-trip-departure-address').textContent = data.departureAddress || '';
    document.getElementById('my-trip-add-dep-address').textContent = data.addDepAddress || '';
    document.getElementById('my-trip-postal-code-dep').textContent = data.postalCodeDep || '';
    document.getElementById('my-trip-arrival-address').textContent = data.arrivalAddress || '';
    document.getElementById('my-trip-add-arr-address').textContent = data.addArrAddress || '';
    document.getElementById('my-trip-postal-code-arr').textContent = data.postalCodeArr || '';

    // Dates et heures
    document.getElementById('my-trip-departure-date').textContent = data.departureDate || '';
    document.getElementById('my-trip-arrival-date').textContent = data.arrivalDate || '';
    document.getElementById('my-trip-departure-time').textContent = data.departureTime || '';
    document.getElementById('my-trip-arrival-time').textContent = data.arrivalTime || '';
    document.getElementById('my-trip-max-travel-time').textContent = data.maxTravelTime ? formatMyTripDuration(data.maxTravelTime) : '';

    // Info du trajet
    const nTickets = data.nTickets || 0;
    document.getElementById('my-trip-n-tickets').textContent = `${nTickets} place${nTickets > 1 ? 's' : ''}`;
    document.getElementById('my-trip-price').textContent = data.price || '';

    // Badge écologique
    const ecoBadgeContainer = document.getElementById('my-trip-eco-travel');
    if (data.eco === 'true') {
        ecoBadgeContainer.innerHTML = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><i class="fas fa-leaf mr-2"></i>Trajet écologique</span>`;
    } else {
        ecoBadgeContainer.innerHTML = '';
    }

    // Info du véhicule
    document.getElementById('my-trip-immat').textContent = data.immat || 'Non disponible';
    document.getElementById('my-trip-brand').textContent = data.brand || 'Non disponible';
    document.getElementById('my-trip-model').textContent = data.model || 'Non disponible';
    document.getElementById('my-trip-color').textContent = data.color || 'Non disponible';
    document.getElementById('my-trip-energie').textContent = data.energie || 'Non disponible';
}

// Formatage de la durée
function formatMyTripDuration(duration) {
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

