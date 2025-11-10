<!-- Block du dashboard: covoiturage futur -->
<section aria-labelledby="reservations-title"
    class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl">
    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
        <h3 id="reservations-title" class="text-xl font-bold text-[#2c3e50]">Mes covoiturages à venir</h3>
    </div>
    <div class="p-6">
        @if ($reservations->isNotEmpty())
            <div class="space-y-4">
                @foreach ($reservations as $reservation)
                    @php
                        $hasPendingSatisfaction =
                            isset($pendingSatisfactions) &&
                            $pendingSatisfactions
                                ->where('covoit_id', $reservation->covoiturage->covoit_id)
                                ->isNotEmpty();
                        $pendingSatisfaction = $hasPendingSatisfaction
                            ? $pendingSatisfactions->where('covoit_id', $reservation->covoiturage->covoit_id)->first()
                            : null;
                    @endphp
                    <div
                        class="flex flex-col sm:flex-row gap-0 {{ $hasPendingSatisfaction ? 'shadow-[0_10px_15px_-3px_rgba(239,68,68,0.8),0_4px_6px_-4px_rgba(239,68,68,0.3)] rounded-lg' : '' }}">
                        <article
                            class="reservation-card bg-white rounded-lg shadow-lg overflow-hidden flex flex-col sm:flex-row transition-transform duration-300 hover:transform hover:-translate-y-1 hover:shadow-xl border border-slate-200 cursor-pointer {{ $hasPendingSatisfaction ? 'flex-grow' : 'w-full' }}"
                            data-confirmation-id="{{ $reservation->conf_id }}" data-user-name="{{ Auth::user()->name }}"
                            data-departure-date="{{ \Carbon\Carbon::parse($reservation->covoiturage->departure_date)->format('d/m/Y') }}"
                            data-departure-time="{{ \Carbon\Carbon::parse($reservation->covoiturage->departure_time)->format('H:i') }}"
                            data-arrival-date="{{ \Carbon\Carbon::parse($reservation->covoiturage->arrival_date)->format('d/m/Y') }}"
                            data-arrival-time="{{ \Carbon\Carbon::parse($reservation->covoiturage->arrival_time)->format('H:i') }}"
                            data-departure-address="{{ $reservation->covoiturage->departure_address }}"
                            data-arrival-address="{{ $reservation->covoiturage->arrival_address }}"
                            data-driver-name="{{ $reservation->covoiturage->user->name }}"
                            data-driver-photo="{{ $reservation->covoiturage->user->photo ? 'data:' . $reservation->covoiturage->user->phototype . ';base64,' . base64_encode($reservation->covoiturage->user->photo) : '' }}"
                            data-driver-rating="{{ $reservation->covoiturage->user->averageRating() }}"
                            data-driver-total-ratings="{{ $reservation->covoiturage->user->totalRatings() }}"
                            data-driver-id="{{ $reservation->covoiturage->user->user_id }}"
                            data-car-brand="{{ $reservation->covoiturage->voiture ? $reservation->covoiturage->voiture->brand : 'Non spécifiée' }}"
                            data-car-model="{{ $reservation->covoiturage->voiture ? $reservation->covoiturage->voiture->model : '' }}"
                            data-car-color="{{ $reservation->covoiturage->voiture ? $reservation->covoiturage->voiture->color : 'Non spécifiée' }}"
                            data-car-energy="{{ $reservation->covoiturage->voiture ? $reservation->covoiturage->voiture->energie : 'Non spécifiée' }}"
                            data-reserved-seats="{{ $reservation->covoiturage->confirmations()->where('user_id', Auth::id())->count() }}"
                            data-max-travel-time="{{ $reservation->covoiturage->max_travel_time }}"
                            data-eco-travel="{{ $reservation->covoiturage->eco_travel }}"
                            data-pref-smoke="{{ $reservation->covoiturage->user->pref_smoke }}"
                            data-pref-pet="{{ $reservation->covoiturage->user->pref_pet }}"
                            data-pref-libre="{{ $reservation->covoiturage->user->pref_libre }}"
                            data-price="{{ $reservation->covoiturage->price }}"
                            data-trip-completed="{{ $reservation->covoiturage->trip_completed }}">
                            <div class="p-4 flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="text-xl font-bold text-gray-800">
                                        <span>{{ $reservation->covoiturage->city_dep }}</span>
                                        <span class="mx-2 text-gray-400">→</span>
                                        <span>{{ $reservation->covoiturage->city_arr }}</span>
                                    </div>
                                    <div class="text-lg font-medium text-gray-700">
                                        <i class="fas fa-calendar-alt mr-2 text-[#2ecc71]"></i>
                                        {{ \Carbon\Carbon::parse($reservation->covoiturage->departure_date)->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <p>Départ à
                                        <b>{{ \Carbon\Carbon::parse($reservation->covoiturage->departure_time)->format('H:i') }}</b>
                                        avec
                                        <b>{{ $reservation->covoiturage->user->name }}</b>
                                    </p>
                                    <p>Voiture :
                                        @if ($reservation->covoiturage->voiture)
                                            {{ $reservation->covoiturage->voiture->brand }}
                                            {{ $reservation->covoiturage->voiture->model }}
                                        @else
                                            Non spécifiée
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="bg-slate-50 p-4 flex flex-col justify-center items-center sm:w-32">
                                <div class="text-2xl font-bold text-[#2ecc71]">{{ $reservation->covoiturage->price }}
                                </div>
                                <div class="text-sm text-gray-500">crédits</div>
                            </div>
                        </article>

                        @if ($hasPendingSatisfaction && $pendingSatisfaction)
                            <button
                                class="satisfaction-btn bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-2 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 sm:w-auto h-auto"
                                data-satisfaction-id="{{ $pendingSatisfaction->satisfaction_id }}"
                                data-covoit-id="{{ $reservation->covoiturage->covoit_id }}"
                                data-driver-name="{{ $reservation->covoiturage->user->name }}"
                                data-trip-date="{{ \Carbon\Carbon::parse($reservation->covoiturage->departure_date)->format('d/m/Y') }}"
                                data-trip-route="{{ $reservation->covoiturage->city_dep }} → {{ $reservation->covoiturage->city_arr }}"
                                onclick="event.stopPropagation(); if(window.openSatisfactionForm) { window.openSatisfactionForm(this.dataset.satisfactionId, this.dataset.covoitId, this.dataset.driverName, this.dataset.tripDate, this.dataset.tripRoute); }">
                                <i class="fas fa-star"></i>
                                <span class="hidden sm:inline">Donner mon avis</span>
                                <span class="sm:hidden">Avis</span>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-slate-500">
                <div class="text-5xl mb-4 text-[#2ecc71]">
                    <i class="fas fa-route"></i>
                </div>
                <p class="mb-4 text-lg">Vous n'avez pas encore de trajet réservé.</p>
                <a href="{{ route('covoiturage') }}"
                    class="inline-block px-6 py-2 bg-[#2ecc71] text-white font-semibold rounded-md hover:bg-[#27ae60] shadow-lg transition-all duration-300 transform hover:scale-105">
                    Rechercher un trajet
                </a>
            </div>
        @endif
    </div>
</section>
