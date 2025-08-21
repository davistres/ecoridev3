<!-- Block du dashboard: profil -->
<div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl">
    <!-- header -->
    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
        <h3 class="text-xl font-bold text-[#2c3e50]">Mon profil</h3>
        <button onclick="openModal('profileModal')"
            class="h-8 w-8 rounded-full bg-[#2ecc71] hover:bg-[#27ae60] text-white flex items-center justify-center transition-transform duration-300 hover:scale-110">
            <i class="fas fa-edit"></i>
        </button>
    </div>

    <!-- Contenu -->
    <div class="p-6">
        <div class="flex flex-col sm:flex-row items-center text-center sm:text-left space-y-4 sm:space-y-0 sm:space-x-6">
            <!-- Place pour la photo -->
            <div class="flex-shrink-0">
                <button onclick="openModal('photoModal')"
                    class="h-28 w-28 rounded-full bg-slate-200 flex items-center justify-center border-4 border-[#2ecc71] shadow-md overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-lg">
                    @if (Auth::user()->photo && Auth::user()->phototype)
                        <img src="data:{{ Auth::user()->phototype }};base64,{{ base64_encode(Auth::user()->photo) }}"
                            alt="Photo de profil" class="h-full w-full object-cover">
                    @else
                        <i class="fas fa-user text-5xl text-slate-500"></i>
                    @endif
                </button>
            </div>

            <!-- Nom et mail -->
            <div class="flex-grow">
                <h4 class="text-2xl font-bold text-[#2c3e50] font-montserrat">{{ $user->name }}</h4>
                <p class="text-slate-600 mt-1"><i
                        class="fas fa-envelope fa-fw mr-2 text-slate-400"></i>{{ $user->email }}</p>

                <!-- N crédit -->
                <div
                    class="mt-4 flex flex-col sm:flex-row items-center justify-center sm:justify-start space-y-3 sm:space-y-0 sm:space-x-4">
                    <div class="flex items-baseline">
                        <span id="credit-balance" class="text-4xl font-bold text-[#2ecc71]">{{ $user->n_credit }}</span>
                        <span class="ml-2 text-lg text-slate-500">crédits</span>
                    </div>
                    <button data-modal-target="recharge-modal"
                        class="recharge-btn px-5 py-2 bg-[#3498db] text-white font-semibold rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg transition-all duration-300 transform hover:scale-105">
                        Recharger
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
