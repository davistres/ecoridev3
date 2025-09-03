<!-- Block Préférences conducteur -->
<div class="bg-white shadow-md rounded-lg p-6 h-full transition-all duration-300 hover:shadow-xl">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gray-800">Préférences conducteur</h3>
        <button onclick="openModal('edit-preferences-modal')"
            class="h-8 w-8 rounded-full bg-[#2ecc71] hover:bg-[#27ae60] text-white flex items-center justify-center transition-transform duration-300 hover:scale-110">
            <i class="fas fa-edit"></i>
        </button>
    </div>
    
    <div class="space-y-3 text-gray-600">
        <p><i class="fas fa-smoking-ban fa-fw mr-2 text-slate-400"></i> {{ Auth::user()->pref_smoke ?? 'Non renseigné' }}</p>
        <p><i class="fas fa-paw fa-fw mr-2 text-slate-400"></i> {{ Auth::user()->pref_pet ?? 'Non renseigné' }}</p>
        @if(Auth::user()->pref_libre)
            <p class="pt-2 border-t border-slate-200"><i class="fas fa-info-circle fa-fw mr-2 text-slate-400"></i> {{ Auth::user()->pref_libre }}</p>
        @endif
    </div>
</div>
