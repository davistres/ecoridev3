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
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Modifier le profil</h2>
            <button onclick="closeModal('profileModal')" class="text-gray-500 hover:text-gray-800">&times;</button>
        </div>

        <form id="profileEditForm" action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div class="form-group">
                    <label for="name" class="block font-semibold">Pseudo</label>
                    <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required
                        class="w-full border rounded-md p-2">
                    <small class="text-slate-500">Maximum 18 caractères.</small>
                </div>
                <div class="form-group">
                    <label for="email" class="block font-semibold">Adresse email</label>
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required
                        class="w-full border rounded-md p-2">
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
