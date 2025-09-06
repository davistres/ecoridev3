// Suivre le véhicule temporaire créé depuis addcovoit-addvehicle-modal dans la modale de covoiturage (create-covoit-modal)

window.temporaryVehicleId = null;

//GESTION DES MODALES ////////////////////////////////////////////
function openModal(modalId, shouldReset = true) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // TODO: j'ai essayer plusieurs façon de ne pas me répéter avec tous les codes qui vont suivre... Mais je n'y suis pas arrivé...
        // Du coup, si j'ai le temps faut que j'essaie de trouver une solution!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        // Réinit edit-preferences-modal à chaque ouverture
        if (modalId === 'edit-preferences-modal' && shouldReset && typeof window.resetEditPreferencesModal === 'function') {
            window.resetEditPreferencesModal();
        }

        // Réinit add-vehicle-modal à chaque ouverture
        if (modalId === 'add-vehicle-modal' && shouldReset && typeof window.resetAddVehicleModal === 'function') {
            window.resetAddVehicleModal();
        }

        // Réinit recharge-modal à chaque ouverture
        if (modalId === 'recharge-modal' && shouldReset && typeof window.resetRechargeModal === 'function') {
            window.resetRechargeModal();
        }

        // Réinit create-covoit-modal à chaque ouverture, SAUF si on vient d'ajouter un véhicule
        if (modalId === 'create-covoit-modal' && shouldReset && typeof window.resetCreateCovoitModal === 'function') {
            window.resetCreateCovoitModal();
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');

        if (modalId === 'modif-covoit-modal' && typeof resetModifCovoitForm === 'function') {
            resetModifCovoitForm();
        }

        // Si on ferme create-covoit-modal, on supprime le véhicule temporaire
        if (modalId === 'create-covoit-modal') {
            // => appel la fonction de reset
            if (typeof window.resetCreateCovoitModal === 'function') {
                window.resetCreateCovoitModal();
            }
        }
    }
}

window.openModal = openModal;
window.closeModal = closeModal;


// GESTION DES NOTIFICATIONS ////////////////////////////////////
function showSuccessNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-5 right-5 bg-green-500 text-white py-3 px-5 rounded-lg shadow-xl z-50 animate-bounce';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function showErrorNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-5 right-5 bg-red-500 text-white py-3 px-5 rounded-lg shadow-xl z-50 animate-bounce';
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

window.showSuccessNotification = showSuccessNotification;
window.showErrorNotification = showErrorNotification;


//Ecouteur d'évent sur toute la page
document.addEventListener('DOMContentLoaded', () => {
    // OUVERTURE DES MODALES///////////////////////////////////
    // Remplace tous les onclick="openModal(...)" pour écouter la tous les clics sur la page
    // Si un clic est détedté sur un élement data_modal-target, on ouvre la modale correspondante
    document.body.addEventListener('click', function(e) {
        const target = e.target.closest('[data-modal-target]');
        if (target) {
            const modalId = target.getAttribute('data-modal-target');
            openModal(modalId);
        }
    });

    //Fermeture des modales                             --
    const modals = document.querySelectorAll('.fixed.inset-0');
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });


    // Modale Photo de Profil (popup.blade.php)
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

    const photoSubmitButton = document.getElementById('profile-photo-submit');
    if (photoSubmitButton) {
        photoSubmitButton.addEventListener('click', function() {
            const photoForm = document.getElementById('profilePhotoForm');
            if (photoForm) {
                photoForm.submit();
            }
        });
    }


    // Modale Changement de Rôle (role.blade.php)
    const roleChangeForms = document.querySelectorAll('.role-change-form');
    if (roleChangeForms.length > 0) {
        roleChangeForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const currentRole = this.dataset.currentRole;
                const newRole = this.querySelector('input[name="role"]:checked').value;
                const warningElement = document.getElementById('role-change-warning');

                if (currentRole === newRole) {
                    if (warningElement) warningElement.classList.remove('hidden');
                    return;
                }

                if (warningElement) warningElement.classList.add('hidden');

                if (currentRole === 'Passager' && (newRole === 'Conducteur' || newRole === 'Les deux')) {
                    const newRoleInput = document.getElementById('new_role_input');
                    if (newRoleInput) newRoleInput.value = newRole;
                    if(typeof window.resetDriverInfoModal === 'function') {
                        window.resetDriverInfoModal();
                    }
                    openModal('driverinfo-modal');
                } else if ((currentRole === 'Conducteur' || currentRole === 'Les deux') && newRole === 'Passager') {
                    openModal('confirm-delete-all-for-change-role-to-passenger-modal');
                    document.getElementById('confirm-role-change-btn').onclick = () => {
                        this.submit();
                    };
                } else {
                    this.submit();
                }
            });
        });
    }




    // Modif véhicule////////////////////////////////////
    document.body.addEventListener('click', function(e) {
        const editButton = e.target.closest('.edit-vehicle-btn');
        if (editButton) {
            const voitureData = JSON.parse(editButton.dataset.voiture);
            openEditVehicleModal(voitureData);
        }
    });
});


//GESTION DES VÉHICULES (CRUD)                      ==
function openEditVehicleModal(voiture) {
    // Réinit la modale avant de la remplir
    if (typeof window.resetEditVehicleModal === 'function') {
        window.resetEditVehicleModal();
    }

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
window.openEditVehicleModal = openEditVehicleModal;


let formToSubmit;
async function confirmVehicleDeletion(event, vehicleCount, vehicleId) {
    event.preventDefault();
    formToSubmit = event.target;

    // Si c'est le dernier
    if (vehicleCount <= 1) {
        openModal('delete-last-vehicle-modal');
        document.getElementById('confirm-delete-last-vehicle-btn').onclick = () => {
            formToSubmit.submit();
        };
        return;
    }

    // Sinon, check s'il y a des covoit?
    try {
        const response = await fetch(`/voitures/${vehicleId}/has-future-carpools`);
        const data = await response.json();

        if (data.has_future_carpools) {
            openModal('confirm-delete-vehicule-with-covoit-modal');
            document.getElementById('confirm-delete-with-carpools-btn').onclick = () => {
                formToSubmit.submit();
            };
        } else {
            // Si pas de covoit
            if (confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')) {
                formToSubmit.submit();
            }
        }
    } catch (error) {
        console.error('Erreur lors de la vérification des covoiturages:', error);
        showErrorNotification('Impossible de vérifier les trajets associés. Veuillez réessayer.');
    }
}
window.confirmVehicleDeletion = confirmVehicleDeletion;
