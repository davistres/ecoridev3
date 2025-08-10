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
});
