<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Espace') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Grand écran -->
            <div class="hidden md:grid md:grid-cols-3 md:grid-rows-2 gap-6">
                <div class="md:col-span-2 md:row-span-1">
                    @include('dashboard.partials.profil', ['user' => $user])
                </div>

                <div class="md:col-start-3 md:row-span-2 h-full flex flex-col">
                    <div class="flex-grow h-full">
                        @include('dashboard.partials.role')
                    </div>
                </div>

                <div class="md:col-span-2 md:row-start-2">
                    @include('dashboard.partials.reservations')
                </div>

                <div class="md:col-span-3 md:row-start-3">
                    @include('dashboard.partials.historique')
                </div>
            </div>

            <!-- Petit écran -->
            <div class="md:hidden space-y-6">
                @include('dashboard.partials.profil', ['user' => $user])
                @include('dashboard.partials.role')
                @include('dashboard.partials.reservations')
                @include('dashboard.partials.historique')
            </div>
        </div>
    </div>

    @include('dashboard.partials.popup')

    <!-- Recharge Modal -->
    <div id="recharge-modal"
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

    <script>
        const rechargeModalId = 'recharge-modal';
        const warningEl = document.getElementById('payment-warning');

        function closeAndResetRechargeModal() {
            closeModal(rechargeModalId);
            if (warningEl) {
                warningEl.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const rechargeBtns = document.querySelectorAll('.recharge-btn');
            const validatePaymentBtn = document.getElementById('validate-payment-btn');
            const creditBalanceEl = document.getElementById('credit-balance');
            const amountOptions = document.querySelectorAll('input[name="recharge_amount"]');
            const fakeInputs = document.querySelectorAll('#recharge-modal input[readonly]');

            // btn pour ouvrir openModal
            rechargeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    openModal(rechargeModalId);
                });
            });

            // Message au clic "Ceci est une version TEST du projet ! Pour recharger votre crédit, sélectionnez juste un montant et validez juste le paiement."
            fakeInputs.forEach(input => {
                input.addEventListener('click', function() {
                    if (warningEl) {
                        warningEl.classList.remove('hidden');
                    }
                });
            });

            // btn activé quand un montant est sélectionné
            amountOptions.forEach(option => {
                option.addEventListener('change', function() {
                    if (this.checked) {
                        validatePaymentBtn.disabled = false;
                        // css pour le sélectionné
                        document.querySelectorAll('.credit-option').forEach(label => label.classList
                            .remove('bg-green-100', 'border-green-500'));
                        this.parentElement.classList.add('bg-green-100', 'border-green-500');
                    }
                });
            });

            // validatePayment
            if (validatePaymentBtn) {
                validatePaymentBtn.addEventListener('click', function() {
                    const selectedAmount = document.querySelector('input[name="recharge_amount"]:checked');
                    if (!selectedAmount) return;

                    const amount = selectedAmount.value;
                    this.disabled = true;

                    fetch('{{ route('credits.recharge') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                amount: amount
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw err;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (creditBalanceEl) {
                                creditBalanceEl.textContent = data.new_credit_balance;
                            }
                            closeAndResetRechargeModal();

                            // Réinit
                            validatePaymentBtn.disabled = true;
                            if (selectedAmount) selectedAmount.checked = false;
                            document.querySelectorAll('.credit-option').forEach(label => label.classList
                                .remove('bg-green-100', 'border-green-500'));
                        })
                        .catch(error => {
                            console.error('There has been a problem with your fetch operation:', error);
                            alert(
                                'Une erreur est survenue. Veuillez vérifier la console pour plus de détails.'
                            );
                            this.disabled = false;
                        });
                });
            }
        });
    </script>
</x-app-layout>
