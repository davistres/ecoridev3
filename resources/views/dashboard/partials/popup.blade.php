<!-- Pop-up photo de profil -->
<div id="photoModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Changer la photo de profil</h2>
            <button onclick="closeModal('photoModal')" class="text-gray-500 hover:text-gray-800">&times;</button>
        </div>

        <div class="profile-photo-container">
            <div class="profile-photo-preview mb-4">
                <h4 class="font-semibold mb-2">Photo actuelle</h4>
                <div
                    class="photo-preview-area w-32 h-32 bg-slate-200 rounded-full mx-auto flex items-center justify-center border-4 border-[#2ecc71] overflow-hidden">
                    @if (Auth::user()->photo && Auth::user()->phototype)
                        <img src="data:{{ Auth::user()->phototype }};base64,{{ base64_encode(Auth::user()->photo) }}"
                            alt="Photo de profil" class="h-full w-full object-cover">
                    @else
                        <i class="fas fa-user text-5xl text-slate-500"></i>
                    @endif
                </div>
            </div>

            <div class="profile-photo-upload">
                <h4 class="font-semibold mb-2">Charger une nouvelle photo</h4>
                <form id="profilePhotoForm" action="{{ route('profile.photo.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <input type="file" id="profile-photo-input" name="profile_photo"
                            accept="image/png,image/jpeg" class="w-full border rounded-md p-2">
                        <small class="text-slate-500">Taille max : 2 Mo. Formats acceptés : PNG, JPEG.</small>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <button onclick="closeModal('photoModal')"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Annuler</button>
            @if (Auth::user()->photo)
                <form action="{{ route('profile.photo.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Supprimer</button>
                </form>
            @endif
            <button type="button" id="profile-photo-submit"
                class="px-4 py-2 bg-[#2ecc71] text-white rounded-md hover:bg-[#27ae60]">Valider</button>
        </div>
    </div>
</div>

<!-- Pop-up profil -->
<div id="profileModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <!-- Header -->
        <div class="flex justify-between items-center p-4 border-b">
            <h2 id="profile-modal-title" class="text-2xl font-bold text-gray-800">Modifier le profil</h2>
            <button onclick="closeModal('profileModal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>

        <!-- Les onglets -->
        <div class="bg-slate-100 p-1">
            <nav class="flex space-x-1" aria-label="Tabs">
                <button id="tab-btn-profil"
                    class="tab-btn flex-1 py-2 px-1 text-center font-medium text-sm rounded-md bg-[#3b82f6] text-[#f1f8e9]">
                    Profil
                </button>
                <button id="tab-btn-password"
                    class="tab-btn flex-1 py-2 px-1 text-center font-medium text-sm rounded-md text-gray-600 hover:bg-slate-200">
                    Mot de passe
                </button>
            </nav>
        </div>

        <!-- Les messages -->
        <div id="password-success-message" class="hidden m-4 p-3 rounded-md bg-[#3b82f6] text-[#f1f8e9] text-center">
            Votre mot de passe a été changé avec succès !
        </div>
        <div id="password-error-message"
            class="hidden m-4 p-3 rounded-md bg-red-100 !text-red-800 font-semibold text-center">
            <!-- Message d'erreur => en js -->
        </div>

        <!-- Onglets -->
        <div class="p-6 pt-0">
            <!-- Onglet profil -->
            <div id="tab-panel-profil">
                <form id="profileEditForm" action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div class="form-group">
                            <label for="name" class="block font-semibold text-gray-700">Pseudo</label>
                            <input type="text" id="name" name="name" value="{{ Auth::user()->name }}"
                                required
                                class="w-full border rounded-md p-2 mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500">
                            <small class="text-slate-500">Maximum 18 caractères.</small>
                        </div>
                        <div class="form-group">
                            <label for="email" class="block font-semibold text-gray-700">Adresse email</label>
                            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}"
                                required
                                class="w-full border rounded-md p-2 mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeModal('profileModal')"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Annuler</button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#2ecc71] text-white rounded-md hover:bg-[#27ae60]">Enregistrer</button>
                    </div>
                </form>
            </div>

            <!-- Onglet mot de passe -->
            <div id="tab-panel-password" class="hidden">
                <form id="passwordUpdateForm" action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block font-semibold text-gray-700">Mot de passe
                                actuel</label>
                            <input type="password" id="current_password" name="current_password" required
                                class="w-full border rounded-md p-2 mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label for="password" class="block font-semibold text-gray-700">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" required
                                class="w-full border rounded-md p-2 mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500">
                            <div id="password-feedback" class="mt-2 text-sm space-y-1">
                                <p id="pass-length" class="text-red-500">✗ Au moins 8 caractères</p>
                                <p id="pass-uppercase" class="text-red-500">✗ Au moins une majuscule</p>
                                <p id="pass-lowercase" class="text-red-500">✗ Au moins une minuscule</p>
                                <p id="pass-number" class="text-red-500">✗ Au moins un chiffre</p>
                                <p id="pass-symbol" class="text-red-500">✗ Au moins un symbole</p>
                            </div>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block font-semibold text-gray-700">Confirmer le
                                nouveau mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full border rounded-md p-2 mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeModal('profileModal')"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Annuler</button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#2ecc71] text-white rounded-md hover:bg-[#27ae60]">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Envoi photoModal
        const photoSubmitBtn = document.getElementById('profile-photo-submit');
        if (photoSubmitBtn) {
            photoSubmitBtn.addEventListener('click', function() {
                document.getElementById('profilePhotoForm').submit();
            });
        }

        // Variable et logique pour les onglets
        const profileModal = document.getElementById('profileModal');
        const tabButtons = profileModal.querySelectorAll('.tab-btn');
        const tabPanels = profileModal.querySelectorAll('[id^="tab-panel-"]');
        const modalTitle = document.getElementById('profile-modal-title');
        const successMessage = document.getElementById('password-success-message');
        const errorMessage = document.getElementById('password-error-message');

        const activeClasses = ['bg-[#3b82f6]', 'text-[#f1f8e9]'];
        const inactiveClasses = ['text-gray-600', 'hover:bg-slate-200'];

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                successMessage.classList.add('hidden');
                errorMessage.classList.add('hidden');

                tabButtons.forEach(btn => {
                    btn.classList.remove(...activeClasses);
                    btn.classList.add(...inactiveClasses);
                });
                tabPanels.forEach(panel => panel.classList.add('hidden'));

                this.classList.add(...activeClasses);
                this.classList.remove(...inactiveClasses);

                const panelId = this.id.replace('-btn-', '-panel-');
                document.getElementById(panelId).classList.remove('hidden');

                modalTitle.textContent = this.textContent.trim() === 'Profil' ?
                    'Modifier le profil' : 'Changer le mot de passe';
            });
        });

        // password-feedback
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            const passLength = document.getElementById('pass-length');
            const passUppercase = document.getElementById('pass-uppercase');
            const passLowercase = document.getElementById('pass-lowercase');
            const passNumber = document.getElementById('pass-number');
            const passSymbol = document.getElementById('pass-symbol');
            const successColor = '#2ecc71';
            const errorColor = '#ef4444';

            const setPasswordRequirementFeedback = (element, isValid) => {
                if (isValid) {
                    element.innerHTML = `✓ ${element.innerText.substring(2)}`;
                    element.style.color = successColor;
                } else {
                    element.innerHTML = `✗ ${element.innerText.substring(2)}`;
                    element.style.color = errorColor;
                }
            };

            passwordInput.addEventListener('input', function() {
                const value = passwordInput.value;
                setPasswordRequirementFeedback(passLength, value.length >= 8);
                setPasswordRequirementFeedback(passUppercase, /[A-Z]/.test(value));
                setPasswordRequirementFeedback(passLowercase, /[a-z]/.test(value));
                setPasswordRequirementFeedback(passNumber, /[0-9]/.test(value));
                setPasswordRequirementFeedback(passSymbol, /[^A-Za-z0-9]/.test(value));
            });
        }

        // Changement de mot de passe
        const passwordUpdateForm = document.getElementById('passwordUpdateForm');
        if (passwordUpdateForm) {
            passwordUpdateForm.addEventListener('submit', function(e) {
                e.preventDefault();
                successMessage.classList.add('hidden');
                errorMessage.classList.add('hidden');

                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': formData.get('_token'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        return response.json().then(data => {
                            throw data;
                        });
                    })
                    .then(data => {
                        if (data.status === 'password-updated') {
                            successMessage.classList.remove('hidden');
                            passwordUpdateForm.reset();
                        }
                    })
                    .catch(errorData => {
                        let errorHtml = 'Une erreur est survenue.';
                        if (errorData.errors) {
                            errorHtml = Object.values(errorData.errors)[0][0];
                        }
                        errorMessage.innerHTML = errorHtml;
                        errorMessage.classList.remove('hidden');
                    });
            });
        }
    });
</script>
