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

    @include('dashboard.partials.popup')
    @include('dashboard.partials.driverinfo-modal')
    @include('dashboard.partials.edit-preferences-modal')
    @include('dashboard.partials.add-vehicle-modal')
    @include('dashboard.partials.edit-vehicle-modal')
    @include('dashboard.partials.delete-last-vehicle-modal')

    <!-- Recharge Modal -->
    <div id="recharge-modal" data-recharge-url="{{ route('credits.recharge') }}"
        class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
        onclick="closeModal('recharge-modal')">
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
                <button type="button" onclick="closeAndResetRechargeModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-all duration-200">Annuler</button>
                <button id="validate-payment-btn" disabled
                    class="px-4 py-2 bg-[#2ecc71] text-white font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg transition-all duration-300 disabled:bg-slate-300 disabled:cursor-not-allowed disabled:shadow-none hover:bg-[#27ae60]">
                    Valider le paiement
                </button>
            </div>
        </div>
    </div>


</x-app-layout>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const creditOptions = document.querySelectorAll('.credit-option');
        const validateBtn = document.getElementById('validate-payment-btn');
        const paymentWarning = document.getElementById('payment-warning');
        const rechargeModal = document.getElementById('recharge-modal');
        const rechargeUrl = rechargeModal.dataset.rechargeUrl;

        let selectedAmount = null;

        creditOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Efface les styles (pour s'assurer par exemple que si on a cliqué sur "20" puis que l'on change d'avis pour "50", le "20" redevient normal.)
                creditOptions.forEach(opt => {
                    opt.classList.remove('border-[#2ecc71]', 'bg-green-50', 'ring-2',
                        'ring-green-300');
                    opt.classList.add('border-slate-200');
                });

                // Applique le style à la sélection
                this.classList.add('border-[#2ecc71]', 'bg-green-50', 'ring-2',
                    'ring-green-300');
                this.classList.remove('border-slate-200');

                selectedAmount = this.querySelector('input[name="recharge_amount"]').value;
                validateBtn.disabled = false;
                paymentWarning.classList.remove('hidden');
            });
        });

        validateBtn.addEventListener('click', function() {
            if (selectedAmount) {
                fetch(rechargeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            amount: selectedAmount
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Maj crédits
                            const creditsElement = document.getElementById('user-credits');
                            if (creditsElement) {
                                creditsElement.textContent = data.new_balance;
                            }
                            // Fermer la modale + notif de succès
                            closeAndResetRechargeModal();
                            showSuccessNotification('Crédits rechargés avec succès !');
                        } else {
                            // Erreur
                            console.error(data.message);
                            alert('Une erreur est survenue lors de la recharge.');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur réseau est survenue.');
                    });
            }
        });
    });

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

    function closeAndResetRechargeModal() {
        closeModal('recharge-modal');
        // Si on annule ou si un paiment a été fait => on réinit tout
        const creditOptions = document.querySelectorAll('.credit-option');
        creditOptions.forEach(opt => {
            opt.classList.remove('border-[#2ecc71]', 'bg-green-50', 'ring-2', 'ring-green-300');
            opt.classList.add('border-slate-200');
            opt.querySelector('input[name="recharge_amount"]').checked = false;
        });
        document.getElementById('validate-payment-btn').disabled = true;
        document.getElementById('payment-warning').classList.add('hidden');
    }

    function showSuccessNotification(message) {
        const notification = document.createElement('div');
        notification.className =
            'fixed bottom-5 right-5 bg-green-500 text-white py-3 px-5 rounded-lg shadow-xl animate-bounce';
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    function openEditVehicleModal(voiture) {
        // Si clic pour modifier une voiture => ça ouvrira ce formulaire pré-rempli
        document.getElementById('edit-brand').value = voiture.brand;
        document.getElementById('edit-model').value = voiture.model;
        document.getElementById('edit-immat').value = voiture.immat;
        document.getElementById('edit-date_first_immat').value = voiture.date_first_immat;
        document.getElementById('edit-color').value = voiture.color;
        document.getElementById('edit-n_place').value = voiture.n_place;
        document.getElementById('edit-energie').value = voiture.energie;

        // Maj
        // Quand on enregistre les modifs => "action" défini l'url vers lequelle les modifs seront envoyées
        const form = document.getElementById('editVehicleForm');
        form.action = `/voitures/${voiture.voiture_id}`;

        openModal('edit-vehicle-modal');
    }

    let formToSubmit;

    function confirmVehicleDeletion(event, vehicleCount) {
        event.preventDefault();
        formToSubmit = event.target;

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
</script>
