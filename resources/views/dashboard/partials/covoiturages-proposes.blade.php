<!-- Block du dashboard: covoiturage proposé -->
<div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl">
    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
        <h3 class="text-xl font-bold text-[#2c3e50]">Mes covoiturages proposés</h3>
        @if ($covoiturages->isNotEmpty())
            <button onclick="openModal('create-covoit-modal')"
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
                <button onclick="openModal('create-covoit-modal')"
                    class="inline-block px-6 py-2 bg-[#3498db] text-white font-semibold rounded-md hover:bg-blue-600 shadow-lg transition-all duration-300 transform hover:scale-105">
                    Proposer un trajet
                </button>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($covoiturages as $covoiturage)
                    @php
                        $departureDate = \Carbon\Carbon::parse($covoiturage->departure_date);
                        $arrivalDate = \Carbon\Carbon::parse($covoiturage->arrival_date);
                        $diffInDays = $departureDate->diffInDays($arrivalDate);
                    @endphp
                    <div class="covoiturage-card bg-white rounded-lg shadow-lg overflow-hidden flex flex-col transition-transform duration-300 hover:transform hover:-translate-y-1 hover:shadow-xl border border-slate-200"
                        data-covoiturage-id="{{ $covoiturage->covoit_id }}">

                        <!-- Header card -->
                        <div class="p-4 bg-slate-50 border-b border-slate-200">
                            <div class="flex justify-between items-center">
                                <div class="text-2xl font-bold text-gray-800">
                                    <span>{{ $covoiturage->city_dep }}</span>
                                    <span class="mx-2 text-gray-400">→</span>
                                    <span>{{ $covoiturage->city_arr }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-medium text-gray-700">
                                        <i class="fas fa-calendar-alt mr-2 text-[#2ecc71]"></i>
                                        {{ $departureDate->format('d/m/Y') }}
                                    </div>
                                    @if ($diffInDays == 1)
                                        <div class="text-sm text-orange-500 font-semibold hidden md:block">Arrivée le
                                            lendemain</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Main card -->
                        <div class="p-6 flex-grow">
                            <!-- Grand écran -->
                            <div class="hidden md:grid md:grid-cols-3 gap-6">
                                <!-- Info covoit -->
                                <div class="space-y-3">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-clock w-6 text-center mr-2 text-[#2ecc71]"></i>
                                        <span>Départ à
                                            <b>{{ \Carbon\Carbon::parse($covoiturage->departure_time)->format('H:i') }}</b></span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-clock w-6 text-center mr-2 text-[#2ecc71]"></i>
                                        <span>Arrivée à
                                            <b>{{ \Carbon\Carbon::parse($covoiturage->arrival_time)->format('H:i') }}</b></span>
                                    </div>
                                </div>
                                <!-- Info vehicule -->
                                <div class="space-y-3">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-car w-6 text-center mr-2 text-slate-500"></i>
                                        <span>{{ $covoiturage->voiture->brand }}
                                            {{ $covoiturage->voiture->model }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-users w-6 text-center mr-2 text-slate-500"></i>
                                        <span>{{ $covoiturage->n_tickets }} places proposées</span>
                                    </div>
                                </div>
                                <!-- Info prix -->
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-[#2ecc71]">{{ $covoiturage->price }} crédits
                                    </div>
                                    <div class="text-sm text-gray-500">par personne</div>
                                </div>
                                @if ($covoiturage->eco_travel)
                                    <div class="md:col-span-3 flex justify-center">
                                        <div
                                            class="inline-flex items-center mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                            <i class="fas fa-leaf mr-2"></i>Voyage écologique
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Petit écran -->
                            <div class="md:hidden space-y-4">
                                <div class="flex justify-between items-center text-gray-600">
                                    <span><i class="fas fa-clock mr-2 text-[#2ecc71]"></i>Départ:
                                        <b>{{ \Carbon\Carbon::parse($covoiturage->departure_time)->format('H:i') }}</b></span>
                                    <span>Arrivée:
                                        <b>{{ \Carbon\Carbon::parse($covoiturage->arrival_time)->format('H:i') }}</b><i
                                            class="fas fa-clock ml-2 text-[#2ecc71]"></i></span>
                                </div>
                                @if ($diffInDays == 1)
                                    <div class="text-center text-red-500 font-bold">Arrivée le lendemain</div>
                                @endif
                                <div class="flex justify-between items-center text-gray-600 pt-2">
                                    <span><i
                                            class="fas fa-car mr-2 text-slate-500"></i>{{ $covoiturage->voiture->brand }}
                                        {{ $covoiturage->voiture->model }}</span>
                                    <span>{{ $covoiturage->n_tickets }} places<i
                                            class="fas fa-users ml-2 text-slate-500"></i></span>
                                </div>
                                @if ($covoiturage->eco_travel)
                                    <div class="text-center">
                                        <div
                                            class="inline-flex items-center mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                            <i class="fas fa-leaf mr-2"></i>Voyage écologique
                                        </div>
                                    </div>
                                @endif
                                <div class="text-center pt-4">
                                    <div class="text-3xl font-bold text-[#2ecc71]">{{ $covoiturage->price }} crédits
                                    </div>
                                    <div class="text-sm text-gray-500">par personne</div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer card (btn) -->
                        <div
                            class="card-footer p-4 bg-slate-50 border-t border-slate-200 grid grid-cols-2 md:flex md:flex-wrap items-center justify-center md:justify-end gap-3">
                            <button
                                class="action-btn w-full md:w-auto px-4 py-2 text-sm font-semibold text-white bg-slate-500 rounded-lg hover:bg-slate-600 transition-colors duration-300">Détails</button>
                            <button onclick="openModifModal(this)" data-covoiturage-id="{{ $covoiturage->covoit_id }}"
                                class="action-btn w-full md:w-auto px-4 py-2 text-sm font-semibold text-white bg-[#3498db] rounded-lg hover:bg-blue-600 transition-colors duration-300">Modifier</button>
                            <form action="{{ route('covoiturages.destroy', $covoiturage) }}" method="POST"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce trajet ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="action-btn w-full md:w-auto px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors duration-300">Annuler</button>
                            </form>
                            <div class="trip-status-toggle" data-trip-id="{{ $covoiturage->covoit_id }}">
                                <button
                                    class="start-trip-btn action-btn w-full md:w-auto px-4 py-2 text-sm font-semibold text-white bg-[#2ecc71] rounded-lg hover:bg-[#27ae60] transition-colors duration-300 {{ !empty($covoiturage->trip_started_at) ? 'hidden' : '' }}">Démarrer</button>
                                <button
                                    class="end-trip-btn action-btn w-full md:w-auto px-4 py-2 text-sm font-bold text-black bg-[#2ecc71] rounded-lg hover:bg-[#27ae60] transition-colors duration-300 {{ empty($covoiturage->trip_started_at) ? 'hidden' : '' }}">Vous
                                    êtes arrivé ?</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <button onclick="resetCreateCovoitForm(); openModal('create-covoit-modal')"
                    class="inline-block px-6 py-2 bg-[#3498db] text-white font-semibold rounded-md hover:bg-blue-600 shadow-lg transition-all duration-300 transform hover:scale-105">
                    Proposer un autre trajet
                </button>
            </div>
        @endif
    </div>
</div>


