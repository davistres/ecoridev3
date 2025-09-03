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
    @include('dashboard.partials.delete-last-vehicle-modal')
    @include('dashboard.partials.confirm-delete-vehicule-with-covoit-modal')
    @include('dashboard.partials.confirm-delete-all-for-change-role-to-passenger-blade')
    @include('dashboard.partials.create-covoit-modal')
    @include('dashboard.partials.modif-covoit-modal')

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

            window.openEditVehicleModal = function(voiture) {
                document.getElementById('edit-brand').value = voiture.brand;
                document.getElementById('edit-model').value = voiture.model;
                document.getElementById('edit-immat').value = voiture.immat;
                document.getElementById('edit-date_first_immat').value = voiture.date_first_immat;
                document.getElementById('edit-color').value = voiture.color;
                document.getElementById('edit-n_place').value = voiture.n_place;
                document.getElementById('edit-energie').value = voiture.energie;
                const form = document.getElementById('editVehicleForm');
                form.action = `/voitures/${voiture.voiture_id}`;
                openModal('edit-vehicle-modal');
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
                }

                // Logique pour les covoit cards
                const covoiturageCards = document.querySelectorAll('.covoiturage-card');
                covoiturageCards.forEach(card => {
                    const tripToggles = card.querySelector('.trip-status-toggle');
                    const modifierBtn = card.querySelector('button[onclick^="openEditVehicleModal"]');
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
            });
        </script>
    @endpush
</x-app-layout>
