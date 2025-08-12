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

window.openModal = openModal;
window.closeModal = closeModal;

// Fermeture en cliquant à l'extérieur de la pop-up
document.addEventListener('DOMContentLoaded', () => {
    const modals = document.querySelectorAll('.fixed.inset-0');
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    // Aperçu dela photo de profil
    const photoInput = document.getElementById('profile-photo-input');
    if (photoInput) {
        photoInput.addEventListener('change', function(event) {
            const previewArea = document.querySelector('.photo-preview-area');
            const file = event.target.files[0];
            if (file && previewArea) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewArea.innerHTML = `<img src="${e.target.result}" alt="Aperçu de la photo" class="h-full w-full object-cover">`;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Envoi
    const photoSubmitButton = document.getElementById('profile-photo-submit');
    if (photoSubmitButton) {
        photoSubmitButton.addEventListener('click', function() {
            const photoForm = document.getElementById('profilePhotoForm');
            if (photoForm) {
                photoForm.submit();
            }
        });
    }

    // Logique pour changer de rôle
    const roleChangeForms = document.querySelectorAll('.role-change-form');
    roleChangeForms.forEach(form => {
        const warningElement = form.querySelector('#role-change-warning');

        form.addEventListener('submit', function (e) {
            const currentRole = this.dataset.currentRole;
            const newRole = this.querySelector('input[name="role"]:checked').value;

            if (currentRole === newRole) {
                e.preventDefault();
                if (warningElement) {
                    warningElement.classList.remove('hidden');
                }
                return;
            }

            if (warningElement) {
                warningElement.classList.add('hidden');
            }

            if (currentRole === 'Passager' && (newRole === 'Conducteur' || newRole === 'Les deux')) {
                e.preventDefault();

                const newRoleInput = document.getElementById('new_role_input');
                if (newRoleInput) {
                    newRoleInput.value = newRole;
                }

                if(typeof openModal === 'function') {
                    openModal('driverinfo-modal');
                }
            }
        });
    });

    // Logique pour la modale de rechargement de crédits
    const rechargeModal = document.getElementById('recharge-modal');

    if (rechargeModal) {
        const rechargeBtns = document.querySelectorAll('.recharge-btn');
        const validatePaymentBtn = document.getElementById('validate-payment-btn');
        const creditBalanceEl = document.getElementById('credit-balance');
        const amountOptions = document.querySelectorAll('input[name="recharge_amount"]');
        const warningEl = document.getElementById('payment-warning');
        const fakeInputs = rechargeModal.querySelectorAll('input[readonly]');
        const rechargeRoute = rechargeModal.dataset.rechargeUrl;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function closeAndResetRechargeModal() {
            closeModal('recharge-modal');
            if (warningEl) {
                warningEl.classList.add('hidden');
            }
            if (validatePaymentBtn) {
                validatePaymentBtn.disabled = true;
            }
            const selectedAmount = document.querySelector('input[name="recharge_amount"]:checked');
            if (selectedAmount) {
                selectedAmount.checked = false;
            }
            document.querySelectorAll('.credit-option').forEach(label => label.classList.remove('bg-green-100', 'border-green-500'));
        }

        const cancelButton = rechargeModal.querySelector('button[onclick*="closeAndResetRechargeModal"]');
        if (cancelButton) {
            cancelButton.setAttribute('onclick', '');
            cancelButton.addEventListener('click', closeAndResetRechargeModal);
        }

        rechargeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                openModal('recharge-modal');
            });
        });

        fakeInputs.forEach(input => {
            input.addEventListener('click', function() {
                if (warningEl) {
                    warningEl.classList.remove('hidden');
                }
            });
        });

        amountOptions.forEach(option => {
            option.addEventListener('change', function() {
                if (this.checked) {
                    if (validatePaymentBtn) {
                        validatePaymentBtn.disabled = false;
                    }
                    document.querySelectorAll('.credit-option').forEach(label => label.classList.remove('bg-green-100', 'border-green-500'));
                    this.parentElement.classList.add('bg-green-100', 'border-green-500');
                }
            });
        });

        if (validatePaymentBtn) {
            validatePaymentBtn.addEventListener('click', function() {
                const selectedAmount = document.querySelector('input[name="recharge_amount"]:checked');
                if (!selectedAmount || !rechargeRoute) return;

                const amount = selectedAmount.value;
                this.disabled = true;

                fetch(rechargeRoute, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            amount: amount
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (creditBalanceEl) {
                            creditBalanceEl.textContent = data.new_credit_balance;
                        }
                        closeAndResetRechargeModal();
                    })
                    .catch(error => {
                        console.error('There has been a problem with your fetch operation:', error);
                        alert('Une erreur est survenue. Veuillez vérifier la console pour plus de détails.');
                        this.disabled = false;
                    });
            });
        }
    }
});
