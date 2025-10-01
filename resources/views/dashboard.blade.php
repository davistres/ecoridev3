<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Espace') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $userRole = Auth::user()->role;
            @endphp

            <!-- Grand écran -->
            <div class="hidden md:grid md:grid-cols-3 gap-6">
                <!--Blocs Profil et Rôle -->
                <div class="md:col-span-2 md:row-span-1">
                    @include('dashboard.partials.profil', ['user' => $user])
                </div>
                <div class="md:col-start-3 md:row-span-2 h-full flex flex-col">
                    <div class="flex-grow h-full">
                        @include('dashboard.partials.role')
                    </div>
                </div>

                <!--Blocs CHAUFFEUR -->
                @if ($userRole === 'Conducteur')
                    <div class="md:col-span-2 md:row-start-2">
                        @include('dashboard.partials.preferences-conducteur')
                    </div>
                    <div class="md:col-span-3 md:row-start-3">
                        @include('dashboard.partials.covoiturages-proposes')
                    </div>
                    <div class="md:col-span-3 md:row-start-4">
                        @include('dashboard.partials.mes-vehicules', ['voitures' => $voitures])
                    </div>
                @endif

                <!--Blocs PASSAGER -->
                @if ($userRole === 'Passager')
                    <div class="md:col-span-2 md:row-start-2">
                        @include('dashboard.partials.reservations')
                    </div>
                @endif

                <!--Blocs LES DEUX -->
                @if ($userRole === 'Les deux')
                    <div class="md:col-span-2 md:row-start-2">
                        @include('dashboard.partials.preferences-conducteur')
                    </div>
                    <div class="md:col-span-3 md:row-start-3">
                        @include('dashboard.partials.reservations')
                    </div>
                    <div class="md:col-span-3 md:row-start-4">
                        @include('dashboard.partials.covoiturages-proposes')
                    </div>
                    <div class="md:col-span-3 md:row-start-5">
                        @include('dashboard.partials.mes-vehicules', ['voitures' => $voitures])
                    </div>
                @endif

                <!--Blocs HISTORIQUE -->
                <div class="md:col-span-3">
                    @include('dashboard.partials.historique')
                </div>
            </div>

            <!-- Petit écran -->
            <div class="md:hidden space-y-6">
                @include('dashboard.partials.profil', ['user' => $user])
                @include('dashboard.partials.role')

                @if ($userRole === 'Conducteur' || $userRole === 'Les deux')
                    @include('dashboard.partials.preferences-conducteur')
                @endif

                @if ($userRole === 'Passager' || $userRole === 'Les deux')
                    @include('dashboard.partials.reservations')
                @endif

                @if ($userRole === 'Conducteur' || $userRole === 'Les deux')
                    @include('dashboard.partials.covoiturages-proposes')

                    @include('dashboard.partials.mes-vehicules', ['voitures' => $voitures])
                @endif

                @include('dashboard.partials.historique')
            </div>
        </div>
    </div>

    <!-- Les modals -->
    @include('dashboard.partials.popup')
    @include('dashboard.partials.driverinfo-modal')
    @include('dashboard.partials.edit-preferences-modal')
    @include('dashboard.partials.add-vehicle-modal')
    @include('dashboard.partials.edit-vehicle-modal')
    @include('dashboard.partials.addcovoit-addvehicle-modal')
    @include('dashboard.partials.delete-last-vehicle-modal')
    @include('dashboard.partials.confirm-delete-vehicule-with-covoit-modal')
    @include('dashboard.partials.confirm-delete-all-for-change-role-to-passenger-blade')
    @include('dashboard.partials.create-covoit-modal')
    @include('dashboard.partials.modif-covoit-modal')
    @include('dashboard.partials.covoiturage-avenir-modal')

    <!-- Recharge Modal -->
    <div id="recharge-modal" data-recharge-url="{{ route('credits.recharge') }}"
        class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Recharger vos crédits</h2>
                <button onclick="closeModal('recharge-modal')"
                    class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
            </div>

            <!-- Body -->
            <div>
                <p class="text-slate-600 mb-4">Sélectionnez le montant à recharger :</p>
                <div id="recharge-amount-options" class="grid grid-cols-3 sm:grid-cols-5 gap-4 mb-6">
                    @foreach ([10, 20, 50, 100, 200] as $amount)
                        <label
                            class="credit-option border-2 border-slate-200 rounded-lg p-3 text-center cursor-pointer hover:border-[#2ecc71] hover:bg-green-50 transition-all duration-200">
                            <input type="radio" name="recharge_amount" value="{{ $amount }}" class="hidden">
                            <span class="text-xl font-bold text-gray-700">{{ $amount }}</span>
                            <span class="text-xs text-slate-500 block">crédits</span>
                        </label>
                    @endforeach
                </div>
                <div id="payment-warning"
                    class="hidden bg-[#3b82f6] text-[#f1f8e9] p-3 my-4 rounded-lg text-sm text-center" role="alert">
                    <p><i class="fas fa-info-circle mr-2"></i>Ceci est une version TEST du projet ! Pour recharger votre
                        crédit, sélectionnez juste un montant et validez juste le paiement.</p>
                </div>

                <!-- Faux formulaire de paiement -->
                <div class="space-y-3 mt-4">
                    <input type="text" placeholder="Nom"
                        class="w-full p-2 border border-slate-300 rounded-md bg-slate-100 cursor-not-allowed" readonly>
                    <input type="text" placeholder="Prénom"
                        class="w-full p-2 border border-slate-300 rounded-md bg-slate-100 cursor-not-allowed" readonly>
                    <input type="text" placeholder="Numéro de carte de crédit"
                        class="w-full p-2 border border-slate-300 rounded-md bg-slate-100 cursor-not-allowed" readonly>
                    <div class="flex gap-4">
                        <input type="text" placeholder="MM/AA"
                            class="flex-1 p-2 border border-slate-300 rounded-md bg-slate-100 cursor-not-allowed"
                            readonly>
                        <input type="text" placeholder="CVC"
                            class="flex-1 p-2 border border-slate-300 rounded-md bg-slate-100 cursor-not-allowed"
                            readonly>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="closeModal('recharge-modal')"
                    class="px-4 py-2 text-sm font-semibold text-white bg-slate-500 rounded-lg hover:bg-slate-600 transition-colors duration-300">Annuler</button>
                <button id="validate-payment-btn" disabled
                    class="px-4 py-2 bg-[#2ecc71] text-white font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg transition-all duration-300 disabled:bg-slate-300 disabled:cursor-not-allowed disabled:shadow-none hover:bg-[#27ae60]">Valider
                    le paiement</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Géné étoiles
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

            // Géné profil du conducteur
            function generateDriverProfileHTML(driver) {
                let photoHTML = '';
                if (driver.driver_photo) {
                    photoHTML =
                        `<img src="${driver.driver_photo}" alt="Photo de ${driver.driver_name}" class="w-16 h-16 rounded-full object-cover border-2 border-green-500">`;
                } else {
                    photoHTML = `<div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center border-2 border-green-500">
                        <i class="fas fa-user text-gray-600 text-xl"></i>
                    </div>`;
                }

                // Géné de la note
                const avgRating = parseFloat(driver.driver_rating);
                const totalRatings = parseInt(driver.driver_total_ratings);
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
                        <h5 class="font-semibold text-gray-800 text-lg">${driver.driver_name}</h5>
                        ${ratingHTML}
                    </div>
                </div>`;
            }

            // Formatage de la durée
            function formatDuration(timeString) {
                if (!timeString || !timeString.includes(':')) {
                    return 'N/A';
                }
                const parts = timeString.split(':');
                const hours = parseInt(parts[0], 10);
                const minutes = parseInt(parts[1], 10);

                if (isNaN(hours) || isNaN(minutes)) {
                    return 'N/A';
                }

                let formatted = '';
                if (hours > 0) {
                    formatted += `${hours}h`;
                }
                if (minutes > 0) {
                    formatted += `${minutes.toString().padStart(2, '0')}`;
                }

                if (formatted === '') {
                    return '0min';
                } else if (!formatted.includes('h')) {
                    return formatted + 'min';
                }

                return formatted;
            }

            // Ouvrir et fermer une modale
            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            }

            // Autres fonctions globales
            window.validateImmat = function(immat) {
                if (!immat) return false;
                const immatUpper = immat.toUpperCase();
                const sivRegex = /^[A-Z]{2}[- ]?\d{3}[- ]?[A-Z]{2}$/;
                const fniRegex = /^\d{1,4}[- ]?[A-Z]{1,3}[- ]?(\d{2}|2[AB])$/;
                return sivRegex.test(immatUpper) || fniRegex.test(immatUpper);
            }

            window.showSuccessNotification = function(message) {
                const notification = document.createElement('div');
                notification.className =
                    'fixed bottom-5 right-5 bg-green-500 text-white py-3 px-5 rounded-lg shadow-xl animate-bounce';
                notification.textContent = message;
                document.body.appendChild(notification);
                setTimeout(() => notification.remove(), 3000);
            }



            window.confirmVehicleDeletion = function(event, vehicleCount) {
                event.preventDefault();
                let formToSubmit = event.target;
                if (vehicleCount > 1) {
                    if (confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')) {
                        formToSubmit.submit();
                    }
                } else {
                    openModal('delete-last-vehicle-modal');
                    document.getElementById('confirm-delete-last-vehicle-btn').onclick = function() {
                        formToSubmit.submit();
                    };
                }
                return false;
            }


            document.addEventListener('DOMContentLoaded', function() {
                // Modale pour recharger le crédit
                const rechargeBtn = document.querySelector('.recharge-btn');
                if (rechargeBtn) {
                    rechargeBtn.addEventListener('click', function() {
                        const modalId = this.dataset.modalTarget;
                        if (modalId) {
                            openModal(modalId);
                        }
                    });
                }

                const rechargeModal = document.getElementById('recharge-modal');
                if (rechargeModal) {
                    const creditOptions = document.querySelectorAll('.credit-option');
                    const validateBtn = document.getElementById('validate-payment-btn');
                    const rechargeUrl = rechargeModal.dataset.rechargeUrl;
                    let selectedAmount = null;

                    // Sélection d'un montant
                    creditOptions.forEach(option => {
                        option.addEventListener('click', function() {
                            creditOptions.forEach(opt => opt.classList.remove('border-[#2ecc71]',
                                'bg-green-50', 'ring-2', 'ring-green-300'));
                            this.classList.add('border-[#2ecc71]', 'bg-green-50', 'ring-2',
                                'ring-green-300');
                            selectedAmount = this.querySelector('input[name="recharge_amount"]').value;
                            validateBtn.disabled = false; // Active le bouton
                        });
                    });

                    // Validation du paiement
                    validateBtn.addEventListener('click', function() {
                        if (selectedAmount) {
                            fetch(rechargeUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        amount: selectedAmount
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        document.querySelectorAll('.credit-balance').forEach(el => el
                                            .textContent = data.new_balance);
                                        closeModal('recharge-modal');
                                        showSuccessNotification('Crédits rechargés avec succès !');
                                    } else {
                                        alert('Une erreur est survenue lors de la recharge.');
                                    }
                                })
                                .catch(() => alert('Une erreur réseau est survenue.'));
                        }
                    });

                    // Afficher l'info quand on clic sur les readonly
                    const readonlyInputs = rechargeModal.querySelectorAll('input[readonly]');
                    const paymentWarning = document.getElementById('payment-warning');

                    readonlyInputs.forEach(input => {
                        input.addEventListener('click', function() {
                            if (paymentWarning) {
                                paymentWarning.classList.remove('hidden');
                            }
                        });
                    });

                    // Réinit pour recharge-modal
                    window.resetRechargeModal = function() {
                        // Désélectionner les options de crédit
                        creditOptions.forEach(opt => opt.classList.remove('border-[#2ecc71]', 'bg-green-50',
                            'ring-2', 'ring-green-300'));

                        // Réinit la variable selectedAmount
                        selectedAmount = null;

                        // Désactive le btn validation
                        validateBtn.disabled = true;

                        // Cache le message d'avertisement
                        if (paymentWarning) {
                            paymentWarning.classList.add('hidden');
                        }
                    }
                }

                // Logique pour les covoit cards
                const covoiturageCards = document.querySelectorAll('.covoiturage-card');
                covoiturageCards.forEach(card => {
                    const tripToggles = card.querySelector('.trip-status-toggle');
                    const modifierBtn = card.querySelector('button[onclick^="openModifModal"]');
                    const annulerForm = card.querySelector('form input[name="_method"][value="DELETE"]')
                        ?.closest('form');
                    const startBtn = card.querySelector('.start-trip-btn');
                    const endBtn = card.querySelector('.end-trip-btn');

                    if (!tripToggles || !modifierBtn || !annulerForm || !startBtn || !endBtn) return;

                    // On annule l'anciennne logique SIMPLE du btn "Annuler" (avec onsubmit) pour lui attribuer deux comportements différents
                    annulerForm.removeAttribute('onsubmit');

                    tripToggles.addEventListener('click', function(event) {
                        const buttonClicked = event.target.closest('button');
                        if (!buttonClicked) return;

                        // Si clic sur "Démarrer" => btn "Modifier" est désactiver + btn "Démarrer" est caché + btn "Vous êtes arrivé ?" est visible
                        if (buttonClicked === startBtn) {
                            modifierBtn.disabled = true;
                            modifierBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            startBtn.classList.add('hidden');
                            endBtn.classList.remove('hidden');
                            card.dataset.tripStarted = 'true';
                        } else if (buttonClicked === endBtn) {
                            // Si clic sur "Vous êtes arrivé ?"...
                            // TODO: la vraie logique!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                            // Pour le moment=> on grise la card + on désactive les btns + on change le texte du btn "Vous êtes arrivé ?" en "Terminé"
                            card.style.opacity = '0.6';
                            card.style.pointerEvents = 'none';
                            card.querySelectorAll('.card-footer .action-btn').forEach(btn => btn
                                .disabled = true);
                            buttonClicked.textContent = 'Terminé';
                        }
                    });

                    // Si clic sur "Annuler" => on check si le trajet a commencé ou pas. Si oui => on réinit la card. Si non => on demande confirmation de suppression
                    annulerForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        if (card.dataset.tripStarted === 'true') {
                            modifierBtn.disabled = false;
                            modifierBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            startBtn.classList.remove('hidden');
                            endBtn.classList.add('hidden');
                            delete card.dataset.tripStarted;
                        } else {
                            if (confirm('Êtes-vous sûr de vouloir annuler ce trajet ?')) {
                                annulerForm.submit();
                            }
                        }
                    });
                });

                // Modale des covoit à venir
                const upcomingTripModal = document.getElementById('covoiturage-avenir-modal');
                if (upcomingTripModal) {
                    const reservationCards = document.querySelectorAll('.reservation-card');
                    const contentDiv = document.getElementById('modal-avenir-content');
                    const closeButtons = upcomingTripModal.querySelectorAll('.modal-close');

                    reservationCards.forEach(card => {
                        card.addEventListener('click', function() {
                            // Récupére toutes les données directement depuis les attributs data
                            const data = {
                                user_name: this.dataset.userName,
                                departure_date: this.dataset.departureDate,
                                departure_time: this.dataset.departureTime,
                                arrival_date: this.dataset.arrivalDate,
                                arrival_time: this.dataset.arrivalTime,
                                departure_address: this.dataset.departureAddress,
                                arrival_address: this.dataset.arrivalAddress,
                                driver_name: this.dataset.driverName,
                                driver_photo: this.dataset.driverPhoto,
                                driver_rating: parseFloat(this.dataset.driverRating),
                                driver_total_ratings: this.dataset.driverTotalRatings,
                                driver_id: this.dataset.driverId,
                                car_brand: this.dataset.carBrand,
                                car_model: this.dataset.carModel,
                                car_color: this.dataset.carColor,
                                car_energy: this.dataset.carEnergy,
                                reserved_seats: parseInt(this.dataset.reservedSeats),
                                max_travel_time: this.dataset.maxTravelTime,
                                eco_travel: this.dataset.ecoTravel === '1',
                                pref_smoke: this.dataset.prefSmoke,
                                pref_pet: this.dataset.prefPet,
                                pref_libre: this.dataset.prefLibre,
                                price: parseFloat(this.dataset.price)
                            };

                            // Texte récap
                            let recapText =
                                `Nous vous rappelons que vous avez acté votre participation à ce trajet. Vous avez réservé ${data.reserved_seats} place(s) pour le covoiturage du ${data.departure_date} à ${data.departure_time}, de ${data.departure_address} vers ${data.arrival_address}.<br><br>`;

                            if (data.reserved_seats > 1) {
                                recapText +=
                                    `Nous vous rappelons aussi que cela vous a coûté ${data.price} crédits pour une place... Soit un total de ${data.price * data.reserved_seats} crédits.`
                            } else {
                                recapText +=
                                    `Nous vous rappelons aussi que cela vous a coûté ${data.price} crédits pour une place...`
                            }


                            // Ouverture de la modale
                            openModal('covoiturage-avenir-modal');

                            // Charger les avis dynamiquement
                            const reviewsContainer = document.getElementById(
                                'modal-avenir-reviews-list');
                            if (window.fetchAndDisplayReviews) {
                                window.fetchAndDisplayReviews(this.dataset.driverId, reviewsContainer);
                            }

                            // La remplir avec les données
                            document.getElementById('modal-avenir-user-name').textContent = data
                                .user_name;
                            document.getElementById('modal-avenir-recap-text').innerHTML = recapText;
                            document.getElementById('modal-avenir-departure-date').textContent = data
                                .departure_date;
                            document.getElementById('modal-avenir-departure-time').textContent = data
                                .departure_time;
                            document.getElementById('modal-avenir-arrival-date').textContent = data
                                .arrival_date;
                            document.getElementById('modal-avenir-arrival-time').textContent = data
                                .arrival_time;
                            document.getElementById('modal-avenir-departure-address').textContent = data
                                .departure_address;
                            document.getElementById('modal-avenir-arrival-address').textContent = data
                                .arrival_address;
                            document.getElementById('modal-avenir-price').textContent = data.price ?
                                `${data.price} crédits` : 'N/A';
                            document.getElementById('modal-avenir-reserved-seats').textContent = data
                                .reserved_seats;
                            document.getElementById('modal-avenir-car-brand').textContent = data
                                .car_brand;
                            document.getElementById('modal-avenir-car-model').textContent = data
                                .car_model;
                            document.getElementById('modal-avenir-car-color').textContent = data
                                .car_color;
                            document.getElementById('modal-avenir-car-energy').textContent = data
                                .car_energy;
                            document.getElementById('modal-avenir-max-travel-time').textContent = data
                                .max_travel_time ? formatDuration(data.max_travel_time) :
                                'Non spécifiée';

                            // Badge éco
                            const ecoBadgeContainer = document.getElementById(
                                'modal-avenir-eco-travel');
                            if (data.eco_travel) {
                                ecoBadgeContainer.innerHTML =
                                    `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><i class="fas fa-leaf mr-2"></i>Trajet écologique</span>`;
                            } else {
                                ecoBadgeContainer.innerHTML = '';
                            }

                            // Préférences
                            document.getElementById('modal-avenir-pref-smoke').textContent = data
                                .pref_smoke || 'Non spécifié';
                            document.getElementById('modal-avenir-pref-pet').textContent = data
                                .pref_pet ? 'Animaux ' + data.pref_pet : 'Non spécifié';
                            const prefLibreContainer = document.getElementById(
                                'modal-avenir-pref-libre-container');
                            const prefLibre = document.getElementById('modal-avenir-pref-libre');
                            if (data.pref_libre) {
                                prefLibre.textContent = data.pref_libre;
                                prefLibreContainer.style.display = 'flex';
                            } else {
                                prefLibreContainer.style.display = 'none';
                            }

                            // Info du conducteur
                            const driverProfileContainer = document.getElementById(
                                'modal-avenir-driver-profile');
                            if (driverProfileContainer) {
                                driverProfileContainer.innerHTML = generateDriverProfileHTML(data);
                            }
                        });
                    });

                    closeButtons.forEach(button => {
                        button.addEventListener('click', () => closeModal('covoiturage-avenir-modal'));
                    });

                    upcomingTripModal.addEventListener('click', function(event) {
                        if (event.target === upcomingTripModal || event.target.classList.contains(
                                'modal-overlay')) {
                            closeModal('covoiturage-avenir-modal');
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
