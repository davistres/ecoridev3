<!-- Block du dashboard: covoiturage futur -->
<div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl">
    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
        <h3 class="text-xl font-bold text-[#2c3e50]">Mes covoiturages à venir</h3>
    </div>
    <div class="p-6 text-center text-slate-500">
        <div class="text-5xl mb-4 text-[#2ecc71]">
            <i class="fas fa-car-side"></i>
        </div>
        <p class="mb-4 text-lg">Vous n'avez pas encore de trajet réservé.</p>
        <a href="{{ route('covoiturage') }}"
            class="inline-block px-6 py-2 bg-[#2ecc71] text-white font-semibold rounded-md hover:bg-[#27ae60] shadow-lg transition-all duration-300 transform hover:scale-105">
            Rechercher un trajet
        </a>
    </div>
</div>
