<!-- Block du dashboard: covoiturage proposé -->
<div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl">
    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
        <h3 class="text-xl font-bold text-[#2c3e50]">Mes covoiturages proposés</h3>
        @if ($covoiturages->isNotEmpty())
            <button data-modal-target="create-covoit-modal"
                class="h-8 w-8 rounded-full bg-[#3498db] hover:bg-blue-600 text-white flex items-center justify-center transition-transform duration-300 hover:scale-110">
                <i class="fas fa-plus"></i>
            </button>
        @endif
    </div>

    <div class="p-6">
        @if ($covoiturages->isEmpty())
            <div class="text-center text-slate-500">
                <div class="text-5xl mb-4 text-[#3498db]">
                    <i class="fas fa-car-side"></i>
                </div>
                <p class="mb-4 text-lg">Vous n'avez pas encore de trajet proposé.</p>
                <button data-modal-target="create-covoit-modal"
                    class="inline-block px-6 py-2 bg-[#3498db] text-white font-semibold rounded-md hover:bg-blue-600 shadow-lg transition-all duration-300 transform hover:scale-105">
                    Proposer un trajet
                </button>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($covoiturages as $covoiturage)
                    <div
                        class="covoiturage-card bg-white rounded-lg shadow-lg overflow-hidden flex flex-col md:flex-row transition-transform duration-300 hover:transform hover:-translate-y-1 hover:shadow-xl">

                        <!-- Section covoit -->
                        <div class="covoiturage-details w-full md:w-2/3 p-6 flex flex-col justify-center">
                            <div class="trip-info-container">
                                <div
                                    class="trip-route flex items-center justify-center text-2xl font-bold text-gray-800 mb-4">
                                    <span class="from">{{ $covoiturage->city_dep }}</span>
                                    <span class="route-arrow mx-4 text-gray-400">→</span>
                                    <span class="to">{{ $covoiturage->city_arr }}</span>
                                </div>
                                <div class="trip-date text-center text-lg font-medium text-gray-700 mb-4">
                                    <i class="fas fa-calendar-alt mr-2 text-green-500"></i>
                                    {{ \Carbon\Carbon::parse($covoiturage->departure_date)->format('d/m/Y') }}
                                </div>
                                <div class="trip-time flex justify-between text-gray-600">
                                    <span class="departure-time">
                                        <i class="fas fa-clock mr-2 text-green-500"></i>
                                        Départ: {{ \Carbon\Carbon::parse($covoiturage->departure_time)->format('H:i') }}
                                    </span>
                                    <span class="arrival-time">
                                        <i class="fas fa-clock mr-2 text-green-500"></i>
                                        Arrivée: {{ \Carbon\Carbon::parse($covoiturage->arrival_time)->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                            @if ($covoiturage->eco_travel)
                                <div
                                    class="trip-eco-badge self-center mt-4 px-4 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                    <i class="fas fa-leaf mr-2"></i>Voyage écologique
                                </div>
                            @endif
                        </div>

                        <!-- Prix et btn -->
                        <div
                            class="covoiturage-booking w-full md:w-1/3 p-6 bg-gray-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col items-center justify-center">
                            <div class="trip-seats text-gray-600 mb-4">
                                <i class="fas fa-user-friends mr-2"></i>
                                {{ $covoiturage->n_tickets }}
                                {{ $covoiturage->n_tickets > 1 ? 'places proposées' : 'place proposée' }}
                            </div>
                            <div class="trip-price text-center mb-4">
                                <span class="price-value text-3xl font-bold text-green-500">{{ $covoiturage->price }}
                                    crédits</span>
                                <span class="price-per-person text-sm text-gray-500"><br>par personne</span>
                            </div>
                            <div class="booking-buttons flex flex-col sm:flex-row gap-2 w-full">
                                <button
                                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center transition-colors duration-300">Détails</button>
                                <button
                                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-center transition-colors duration-300">Annuler</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <button data-modal-target="create-covoit-modal"
                    class="inline-block px-6 py-2 bg-[#3498db] text-white font-semibold rounded-md hover:bg-blue-600 shadow-lg transition-all duration-300 transform hover:scale-105">
                    Proposer un autre trajet
                </button>
            </div>
        @endif
    </div>
</div>
