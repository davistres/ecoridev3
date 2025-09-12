<x-app-layout>
    <div class="covoiturage-container max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Rechercher un covoiturage</h1>

        <!-- Messages d'alerte -->
        @if ($searchPerformed && empty($errors))
            <!-- Suggestions de dates -->
            @if (session('suggestions'))
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6 text-left max-w-4xl mx-auto">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">💡 Suggestions de dates alternatives :</h4>
                    <div class="text-sm text-blue-700">
                        <p>Nous n'avons pas de covoiturage à la date recherchée. Néanmoins, nous en avons
                            @foreach (session('suggestions') as $index => $suggestion)
                                @if ($index == 0)
                                    <a href="#"
                                        class="suggestion-link text-blue-600 hover:text-blue-800 underline font-medium"
                                        data-depart="{{ session('lieu_depart') }}"
                                        data-arrivee="{{ session('lieu_arrivee') }}" data-date="{{ $suggestion['date'] }}"
                                        data-seats="{{ session('requested_seats') }}">
                                        {{ $suggestion['count'] }} le {{ $suggestion['formatted_date'] }}
                                        ({{ $suggestion['relative_day'] }})
                                    </a>
                                @elseif ($index == count(session('suggestions')) - 1)
                                    et
                                    <a href="#"
                                        class="suggestion-link text-blue-600 hover:text-blue-800 underline font-medium"
                                        data-depart="{{ session('lieu_depart') }}"
                                        data-arrivee="{{ session('lieu_arrivee') }}"
                                        data-date="{{ $suggestion['date'] }}"
                                        data-seats="{{ session('requested_seats') }}">
                                        @if ($suggestion['count'] > 1)
                                            {{ $suggestion['count'] }}
                                        @endif le {{ $suggestion['formatted_date'] }}
                                        ({{ $suggestion['relative_day'] }})
                                    </a>
                                @else
                                    ,
                                    <a href="#"
                                        class="suggestion-link text-blue-600 hover:text-blue-800 underline font-medium"
                                        data-depart="{{ session('lieu_depart') }}"
                                        data-arrivee="{{ session('lieu_arrivee') }}"
                                        data-date="{{ $suggestion['date'] }}"
                                        data-seats="{{ session('requested_seats') }}">
                                        @if ($suggestion['count'] > 1)
                                            {{ $suggestion['count'] }}
                                        @endif le {{ $suggestion['formatted_date'] }}
                                        ({{ $suggestion['relative_day'] }})
                                    </a>
                                @endif
                            @endforeach
                            ... Si vous êtes flexible, ils n'attendent que vous !
                        </p>
                    </div>
                </div>
            @elseif (session('distant_dates'))
                <!-- Message pour dates distantes -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6 text-left max-w-4xl mx-auto">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">Trajets disponibles mais éloignés de
                                votre
                                date</h4>
                            <p class="text-sm text-yellow-700 mb-3">
                                Les trajets entre <strong>{{ session('lieu_depart') }}</strong> et
                                <strong>{{ session('lieu_arrivee') }}</strong> sont assez loin de la date voulue.
                            </p>
                            <div class="text-sm text-yellow-700 mb-3">
                                @if (session('distant_dates')['closest_before'])
                                    <p>• <strong>Avant votre date</strong>, le premier que l'on a est le :
                                        <a href="#"
                                            class="suggestion-link text-yellow-800 hover:text-yellow-900 underline font-medium"
                                            data-depart="{{ session('lieu_depart') }}"
                                            data-arrivee="{{ session('lieu_arrivee') }}"
                                            data-date="{{ session('distant_dates')['closest_before']['date'] }}"
                                            data-seats="{{ session('requested_seats') }}">
                                            {{ session('distant_dates')['closest_before']['formatted_date'] }}
                                        </a>
                                    </p>
                                @endif
                                @if (session('distant_dates')['closest_after'])
                                    <p>• <strong>Après votre date</strong>, nous en avons un le :
                                        <a href="#"
                                            class="suggestion-link text-yellow-800 hover:text-yellow-900 underline font-medium"
                                            data-depart="{{ session('lieu_depart') }}"
                                            data-arrivee="{{ session('lieu_arrivee') }}"
                                            data-date="{{ session('distant_dates')['closest_after']['date'] }}"
                                            data-seats="{{ session('requested_seats') }}">
                                            {{ session('distant_dates')['closest_after']['formatted_date'] }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                            <p class="text-sm text-yellow-700">
                                Si cela ne vous convient pas, et si votre situation le permet, nous vous conseillons de
                                cibler des
                                villes proches de votre ville de départ et d'arrivée. Ou d'essayer de découper votre
                                trajet
                                en
                                plusieurs arrêts... Ainsi, nous espérons que vous aurez plus de choix... Bonne route !
                                🚗
                            </p>
                        </div>
                    </div>
                </div>
            @elseif (session('insufficient_seats_cumulative'))
                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6 text-left max-w-4xl mx-auto">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-green-800 mb-2">Covoiturages disponibles le jour
                                souhaité
                            </h4>
                            <p class="text-sm text-green-700 mb-3">
                                Le jour souhaité, nous avons des covoiturages entre
                                <strong>{{ session('lieu_depart') }}</strong>
                                et <strong>{{ session('lieu_arrivee') }}</strong>.
                                Cependant, aucun, individuellement, n'a le nombre de places désiré
                                ({{ session('requested_seats') }} places).
                            </p>
                            <p class="text-sm text-green-700">
                                Si votre situation le permet, nous vous conseillons de réserver plusieurs covoiturages
                                pour
                                atteindre la réservation voulue !
                                <strong>Total disponible : {{ session('total_seats_today') }} places</strong>. Bonne
                                route
                                ! 🚗
                            </p>
                        </div>
                    </div>
                </div>
            @elseif (session('insufficient_seats_alternatives'))
                <div class="bg-orange-50 border border-orange-200 rounded-md p-4 mb-6 text-left max-w-4xl mx-auto">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-orange-800 mb-2">Places limitées le jour souhaité</h4>
                            <p class="text-sm text-orange-700 mb-3">
                                Pour les villes choisis, à la date recherchée, nous avons uniquement
                                {{ count(session('trips_today')) > 1 ? 'les propositions' : 'la proposition' }} que
                                vous
                                voyez.
                                Le jour voulu, nous n'avons donc pas assez de places à vous proposer
                                ({{ session('requested_seats') }} demandées, {{ session('total_seats_today') }}
                                disponibles).
                            </p>
                            @if (session('seat_alternatives') && count(session('seat_alternatives')) > 0)
                                <p class="text-sm text-orange-700 mb-2">
                                    Cependant, si votre situation vous le permet, nous vous informons que nous avons
                                    d'autres
                                    alternatives :
                                </p>
                                <div class="text-sm text-orange-700">
                                    @foreach (session('seat_alternatives') as $index => $alternative)
                                        @if ($index > 0)
                                            ,
                                        @endif
                                        le <a href="#"
                                            class="suggestion-link text-orange-800 hover:text-orange-900 underline font-medium"
                                            data-depart="{{ session('lieu_depart') }}"
                                            data-arrivee="{{ session('lieu_arrivee') }}"
                                            data-date="{{ $alternative['date'] }}"
                                            data-seats="{{ session('requested_seats') }}"><strong>{{ $alternative['formatted_date'] }}</strong></a>
                                        ({{ $alternative['relative_day'] }})
                                        nous avons un total de
                                        <strong>{{ $alternative['total_seats'] }} places</strong>
                                    @endforeach
                                    . Vous pouvez même les cumuler (si besoin) !!!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif (session('distant_perfect_matches'))
                <div class="bg-purple-50 border border-purple-200 rounded-md p-4 mb-6 text-left max-w-4xl mx-auto">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-purple-800 mb-2">Correspondances parfaites à des dates
                                différentes</h4>
                            <p class="text-sm text-purple-700 mb-3">
                                Nous vous informons que nous avons bien un ou des résultats correspondants à vos
                                critères
                                ({{ session('requested_seats') }} places entre
                                <strong>{{ session('lieu_depart') }}</strong> et
                                <strong>{{ session('lieu_arrivee') }}</strong>)
                                mais à différentes dates.
                            </p>
                            @if (session('perfect_matches'))
                                <p class="text-sm text-purple-700 mb-3">
                                    Si votre situation vous le permet, vous pourrez les trouver
                                    @if (isset(session('perfect_matches')['before']) && isset(session('perfect_matches')['after']))
                                        le <strong><a href="#"
                                                class="suggestion-link text-purple-800 hover:text-purple-900 underline font-medium"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}"
                                                data-date="{{ session('perfect_matches')['before']['date'] }}"
                                                data-seats="{{ session('requested_seats') }}">{{ session('perfect_matches')['before']['formatted_date'] }}</a></strong>
                                        et
                                        le
                                        <strong><a href="#"
                                                class="suggestion-link text-purple-800 hover:text-purple-900 underline font-medium"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}"
                                                data-date="{{ session('perfect_matches')['after']['date'] }}"
                                                data-seats="{{ session('requested_seats') }}">{{ session('perfect_matches')['after']['formatted_date'] }}</a></strong>.
                                    @elseif (isset(session('perfect_matches')['before']))
                                        le <strong><a href="#"
                                                class="suggestion-link text-purple-800 hover:text-purple-900 underline font-medium"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}"
                                                data-date="{{ session('perfect_matches')['before']['date'] }}"
                                                data-seats="{{ session('requested_seats') }}">{{ session('perfect_matches')['before']['formatted_date'] }}</a></strong>.
                                    @elseif (isset(session('perfect_matches')['after']))
                                        le <strong><a href="#"
                                                class="suggestion-link text-purple-800 hover:text-purple-900 underline font-medium"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}"
                                                data-date="{{ session('perfect_matches')['after']['date'] }}"
                                                data-seats="{{ session('requested_seats') }}">{{ session('perfect_matches')['after']['formatted_date'] }}</a></strong>.
                                    @endif
                                </p>
                            @endif
                            <p class="text-sm text-purple-700">
                                Nous espérons que ces trajets ne seront pas trop lointains pour vous... À défaut, si
                                cela ne
                                vous
                                satisfait pas,
                                vous pouvez démultiplier les possibilités en réservant plusieurs covoiturages, en
                                choisissant des
                                villes proches
                                de celles désirées ou en découpant votre trajet en plusieurs covoiturages. En faisant
                                cela,
                                nous
                                espérons que vous
                                trouverez votre bonheur ! Bonne route ! 🚗
                            </p>
                        </div>
                    </div>
                </div>
            @elseif (session('general_criteria_mismatch'))
                <!-- Si on ne peut rien proposer d'acceptable -->
                <div class="bg-gray-50 border border-gray-200 rounded-md p-4 mb-6 text-left max-w-4xl mx-auto">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-gray-800 mb-2">Critères non satisfaits</h4>
                            <p class="text-sm text-gray-700 mb-3">
                                Nous vous informons que nous avons bien un ou des trajets entre
                                <strong>{{ session('lieu_depart') }}</strong> et
                                <strong>{{ session('lieu_arrivee') }}</strong>... Cependant, nous ne pouvons
                                satisfaire tous vos autres critères... Et actuellement, nous ne pouvons même pas vous
                                proposer des alternatives acceptables... Nous comprenons votre déception ! Cependant, si
                                votre situation vous le permet, nous vous invitons à essayer de démultiplier les
                                possibilités en cherchant des trajets dans des villes proches de celles désirées, en
                                découpant votre trajet en plusieurs covoiturage, en changeant le nombre de place et même
                                pourquoi pas, en changeant la date. En faisant cela, nous espérons que vous trouverez
                                votre bonheur ! Bonne route ! 🚗
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <section class="search-section bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-4xl mx-auto mb-12">
            <!-- Message d'erreur de validation -->
            @if (isset($errors) && !empty($errors))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erreur dans la recherche</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Info sur les codes postaux => cachée par défaut => apparait au focus sur les champs -->
            <div id="postal-code-info" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md"
                style="display: none;">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Information importante</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Les adresses doivent obligatoirement contenir un <strong>code postal</strong> (format:
                                12345 ou 12 345) pour effectuer la recherche.</p>
                            <p class="mt-1"><strong>Exemple :</strong> "123 rue de la Paix, 75001 Paris" ou "Gare
                                SNCF
                                69000 Lyon"</p>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('covoiturage') }}" method="GET"
                class="grid grid-cols-1 lg:grid-cols-10 gap-4 items-end">


                <!--Honeypot-->
                <div class="hidden">
                    <label for="raison_sociale">Raison Sociale</label>
                    <input type="text" id="raison_sociale" name="raison_sociale" tabindex="-1"
                        autocomplete="off">
                </div>

                <!-- Départ -->
                <div class="lg:col-span-3">
                    <label for="departure" class="block text-sm font-medium text-gray-700 text-left">Départ</label>
                    <input type="text" id="departure" name="departure" value="{{ $input['departure'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Ex: 123 rue de la Paix, 75001 Paris" required>
                </div>

                <!-- Arrivée -->
                <div class="lg:col-span-3">
                    <label for="arrival" class="block text-sm font-medium text-gray-700 text-left">Arrivée</label>
                    <input type="text" id="arrival" name="arrival" value="{{ $input['arrival'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Ex: Gare SNCF 69000 Lyon" required>
                </div>

                <!-- Date -->
                <div class="lg:col-span-2">
                    <label for="date" class="block text-sm font-medium text-gray-700 text-left">Date</label>
                    <input type="date" id="date" name="date" value="{{ $input['date'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required>
                </div>

                <!-- n place -->
                <div class="lg:col-span-1">
                    <label for="seats" class="block text-sm font-medium text-gray-700 text-left">Places</label>
                    <input type="number" id="seats" name="seats" min="1" max="8"
                        value="{{ $input['seats'] ?? '' }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="≤8" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>

                <!-- btn recherche -->
                <div class="lg:col-span-1">
                    <button type="submit"
                        class="w-full flex items-center justify-center bg-green-600 text-white font-bold py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 lg:w-auto">
                        <span class="lg:hidden mr-2">RECHERCHER</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
            <div id="seats-warning" class="text-red-500 text-sm mt-2 text-justify" style="display: none;">
                La majorité des trajets proposés sur la plateforme n’ont que entre 1 et 4 places de libres. Les
                véhicules standards n’ont généralement pas plus de 6 places (hors siège du chauffeur). Pour
                maximiser vos chances, nous vous invitons à chercher plusieurs trajets vers votre destination.
            </div>
        </section>

        <!-- Section des résultats ou message si pas de résultats -->
        @if ($covoiturages->isNotEmpty())
            <div class="results-title flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Trajets disponibles</h2>
                <p class="text-gray-600">{{ $covoiturages->count() }} résultat(s) trouvé(s)</p>
            </div>
            <section class="covoiturage-list grid gap-6">
                @foreach ($covoiturages as $covoiturage)
                    <!-- covoiturage-card !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!-->
                    <div
                        class="covoiturage-card bg-white rounded-lg shadow-lg overflow-hidden flex flex-col md:flex-row transition-transform duration-300 hover:transform hover:-translate-y-1 hover:shadow-xl">
                        <div
                            class="covoiturage-driver w-full md:w-1/4 p-6 bg-gray-50 border-b md:border-b-0 md:border-r border-gray-200 flex flex-col items-center justify-center text-center">
                            <div
                                class="driver-photo w-24 h-24 rounded-full border-4 border-green-400 shadow-md mb-4 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user text-4xl text-gray-500"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $covoiturage->user->name }}</h3>
                            <div class="driver-rating flex items-center gap-2 mt-1">
                                @if ($covoiturage->user->average_rating && $covoiturage->user->total_ratings > 0)
                                    <span
                                        class="rating-value font-bold text-yellow-500">{{ number_format($covoiturage->user->average_rating, 1) }}/5</span>
                                    <span class="rating-stars text-yellow-500"
                                        data-rating="{{ $covoiturage->user->average_rating }}"></span>
                                    <span class="text-xs text-gray-500">({{ $covoiturage->user->total_ratings }}
                                        avis)</span>
                                @else
                                    <span class="rating-value font-bold text-yellow-500">Nouveau conducteur</span>
                                    <span class="rating-stars text-yellow-500" data-rating="0">
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="covoiturage-details w-full md:w-1/2 p-6 flex flex-col justify-center">
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
                                        Départ:
                                        {{ \Carbon\Carbon::parse($covoiturage->departure_time)->format('H:i') }}
                                    </span>
                                    <span class="arrival-time">
                                        <i class="fas fa-clock mr-2 text-green-500"></i>
                                        Arrivée:
                                        {{ \Carbon\Carbon::parse($covoiturage->arrival_time)->format('H:i') }}
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
                        <div
                            class="covoiturage-booking w-full md:w-1/4 p-6 bg-gray-50 border-t md:border-t-0 md:border-l border-gray-200 flex flex-col items-center justify-center">
                            <div class="trip-seats text-gray-600 mb-4">
                                <i class="fas fa-user-friends mr-2"></i>
                                {{ $covoiturage->n_tickets }}
                                {{ $covoiturage->n_tickets > 1 ? 'places disponibles' : 'place disponible' }}
                            </div>
                            <div class="trip-price text-center mb-4">
                                <span class="price-value text-3xl font-bold text-green-500">{{ $covoiturage->price }}
                                    crédits</span>
                                <span class="price-per-person text-sm text-gray-500">
                                    <br>par personne</span>
                            </div>
                            <div class="booking-buttons flex flex-col gap-2 w-full">
                                <a href="#"
                                    class="btn-details bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center transition-colors duration-300"
                                    data-id="{{ $covoiturage->covoit_id }}">
                                    Détails
                                </a>
                                <a href="#"
                                    class="btn-participate bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-center transition-colors duration-300">
                                    Participer
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @else
            <!-- Message si pas de résultats -->
            @if ($searchPerformed && empty($errors) && session('no_trips_between_cities'))
                <div class="text-center mt-12">
                    <div
                        class="inline-block bg-red-50 border-2 border-red-200 rounded-2xl p-8 shadow-sm max-w-2xl mx-auto">
                        <div class="mb-6">
                            <svg class="mx-auto h-16 w-16 text-red-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">Aucun trajet disponible</h2>
                        <p class="text-gray-600 text-lg mb-6">
                            Aucun trajet entre <strong>{{ session('lieu_depart') }}</strong> et
                            <strong>{{ session('lieu_arrivee') }}</strong> n'est disponible pour le moment.
                        </p>
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6 text-left">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">💡 Nos conseils :</h4>
                            <p class="text-sm text-blue-700 mb-2">
                                Si votre situation le permet, pour avoir plus d'option, nous vous conseillons
                                soit de chercher des
                                correspondances entre des villes proches de votre lieu de départ et d'arrivée. Soit de
                                découper votre trajet en plusieurs covoiturages.
                            </p>
                            <p class="text-sm text-blue-700">
                                Nous espérons ainsi que vous trouverez votre bonheur... Bonne route ! 🚗
                            </p>
                        </div>
                        <button onclick="resetSearchForm()"
                            class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition-colors font-medium">
                            🔄 Nouvelle recherche
                        </button>
                    </div>
                </div>
            @else
                <div class="text-center mt-12">
                    <div
                        class="inline-block bg-green-50 border-2 border-green-200 rounded-2xl p-8 shadow-sm max-w-2xl mx-auto">
                        <img src="https://img.icons8.com/color/96/000000/carpool.png" alt="Icône de covoiturage"
                            class="mx-auto mb-6 h-20 w-20">
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">
                            Bienvenue sur la page de covoiturage
                        </h2>
                        <p class="text-gray-600 text-lg mb-6">
                            Utilisez le formulaire ci-dessus pour trouver votre prochain trajet écologique et
                            économique.
                        </p>
                        <div class="bg-white rounded-lg p-6 text-left text-gray-700">
                            <h3 class="font-semibold text-xl mb-3 text-green-700">Conseils pour votre recherche :
                            </h3>
                            <ul class="list-disc list-inside space-y-2">
                                <li>Soyez précis sur les noms de villes</li>
                                <li>Essayez différentes dates pour plus d'options</li>
                                <li>Les voyages écologiques sont indiqués par un badge vert</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Formulaire caché pour les suggestions -->
        <form id="suggestion-form" action="{{ route('covoiturage') }}" method="GET" style="display: none;">
            <input type="hidden" id="suggestion-departure" name="departure" value="">
            <input type="hidden" id="suggestion-arrival" name="arrival" value="">
            <input type="hidden" id="suggestion-date" name="date" value="">
            <input type="hidden" id="suggestion-seats" name="seats" value="">
        </form>
    </div>

    <!-- Modale -->
    <div class="modal fixed w-full h-full top-0 left-0 flex items-center justify-center hidden" id="tripDetailsModal">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-3xl mx-auto rounded shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-6">
                <div class="modal-header flex justify-between items-center pb-3">
                    <p class="text-2xl font-bold">Détails du covoiturage</p>
                    <div class="modal-close cursor-pointer z-50">
                        <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18"
                            height="18" viewBox="0 0 18 18">
                            <path
                                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                            </path>
                        </svg>
                    </div>
                </div>

                <div class="modal-body">
                    <!-- Contenu chargé par JS -->
                </div>

                <div class="modal-footer flex justify-end pt-2">
                    <button
                        class="modal-close-btn px-4 bg-transparent p-3 rounded-lg text-indigo-500 hover:bg-gray-100 hover:text-indigo-400 mr-2">Fermer</button>
                    <a href="#"
                        class="btn-participate-modal px-4 bg-indigo-500 p-3 rounded-lg text-white hover:bg-indigo-400">Participer</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Réinit la page
            function resetSearchForm() {
                window.location.href = '{{ route('covoiturage') }}';
            }

            document.addEventListener("DOMContentLoaded", function() {
                window.resetSearchForm = resetSearchForm;

                // Logique de la modale
                const modal = document.getElementById('tripDetailsModal');
                // Execute le code que si la modale existe
                if (modal) {
                    const closeButtons = modal.querySelectorAll('.modal-close, .modal-close-btn');
                    const detailsButtons = document.querySelectorAll('.btn-details');

                    detailsButtons.forEach(button => {
                        button.addEventListener('click', function(event) {
                            event.preventDefault();
                            const tripId = this.getAttribute('data-id');
                            // TODO: appel fetch pour obtenir les détails du voyage et remplir la modale.
                            modal.classList.remove('hidden');
                        });
                    });

                    closeButtons.forEach(button => {
                        button.addEventListener('click', () => {
                            modal.classList.add('hidden');
                        });
                    });

                    modal.addEventListener('click', function(event) {
                        if (event.target === modal) {
                            modal.classList.add('hidden');
                        }
                    });
                }

                // Logique pour les étoiles
                function generateStars(rating) {
                    let starsHtml = '';
                    const fullStars = Math.floor(rating);
                    const hasHalfStar = rating - fullStars >= 0.5;

                    for (let i = 0; i < fullStars; i++) {
                        starsHtml += '<i class="fas fa-star"></i>';
                    }

                    if (hasHalfStar) {
                        starsHtml += '<i class="fas fa-star-half-alt"></i>';
                    }

                    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
                    for (let i = 0; i < emptyStars; i++) {
                        starsHtml += '<i class="far fa-star"></i>';
                    }

                    return starsHtml;
                }

                document.querySelectorAll('.rating-stars').forEach(starContainer => {
                    const rating = parseFloat(starContainer.getAttribute('data-rating'));
                    if (!isNaN(rating) && rating > 0) {
                        starContainer.innerHTML = generateStars(rating);
                    }
                });

                // Choix de la date => à partir de la date d'aujourd'hui
                const dateInput = document.getElementById('date');
                if (dateInput) {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = ('0' + (today.getMonth() + 1)).slice(-2);
                    const day = ('0' + today.getDate()).slice(-2);
                    dateInput.min = `${year}-${month}-${day}`;
                }

                // Avertissement utilisateur si ils choissisent plus de 5 places
                const seatsInput = document.getElementById('seats');
                const seatsWarning = document.getElementById('seats-warning');
                if (seatsInput) {
                    seatsInput.addEventListener('input', function() {
                        const seats = parseInt(this.value, 10);
                        if (seats >= 5) {
                            seatsWarning.style.display = 'block';
                        } else {
                            seatsWarning.style.display = 'none';
                        }
                    });
                }

                // Affichage des infos sur les codes postaux
                const departureInput = document.getElementById('departure');
                const arrivalInput = document.getElementById('arrival');
                const postalCodeInfo = document.getElementById('postal-code-info');
                const errorMessagesContainer = document.querySelector('.bg-red-50');

                // Afficher les infos si il n'y a pas d'erreur
                function showPostalCodeInfo() {
                    const hasVisibleErrors = errorMessagesContainer && errorMessagesContainer.offsetParent !== null;
                    if (!hasVisibleErrors && postalCodeInfo) {
                        postalCodeInfo.style.display = 'block';
                    }
                }

                // Fonction => cache les infos si ya des erreurs
                function hidePostalCodeInfoIfErrors() {
                    const hasVisibleErrors = errorMessagesContainer && errorMessagesContainer.offsetParent !== null;
                    if (hasVisibleErrors && postalCodeInfo) {
                        postalCodeInfo.style.display = 'none';
                    }
                }

                // Affiche => info au focus sur les champs départ/arrivée
                // A RETENIR => au clic et au focus, ce n'est pas du tout pareil... Ici, au focus est mieux (car ça peut être au clic, mais aussi avec la touche tab)... Normalement, il y a d'autre avantage (avec le focus) que je n'ai pas vraiment compris... A RELIRE!!!!!!
                if (departureInput) {
                    departureInput.addEventListener('focus', showPostalCodeInfo);
                }
                if (arrivalInput) {
                    arrivalInput.addEventListener('focus', showPostalCodeInfo);
                }

                // Cacher l'info si il y a des erreurs
                hidePostalCodeInfoIfErrors();

                // Liens de suggestions
                const suggestionLinks = document.querySelectorAll('.suggestion-link');
                const suggestionForm = document.getElementById('suggestion-form');
                suggestionLinks.forEach(function(link) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (suggestionForm) {
                            suggestionForm.querySelector('#suggestion-departure').value = this
                                .getAttribute(
                                    'data-depart');
                            suggestionForm.querySelector('#suggestion-arrival').value = this
                                .getAttribute(
                                    'data-arrivee');
                            suggestionForm.querySelector('#suggestion-date').value = this.getAttribute(
                                'data-date');
                            suggestionForm.querySelector('#suggestion-seats').value = this.getAttribute(
                                'data-seats');
                            suggestionForm.submit();
                        }
                    });
                });

                // Restriction des caractères pour les champs Départ et Arrivée
                const departureField = document.getElementById('departure');
                const arrivalField = document.getElementById('arrival');

                // Pour le 1er caractère : lettres, lettres accentuées ou chiffres
                const firstCharRegex =
                    /^[a-zA-Z0-9àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĸĹĺĻļĽľĿŀŁłŃńŅņŇňŉŊŋŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽž]$/;

                // Pour les suivants : lettres, lettres accentuées, chiffres, espaces et caractères spéciaux autorisés
                const allowedCharsRegex =
                    /^[a-zA-Z0-9àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĸĹĺĻļĽľĿŀŁłŃńŅņŇňŉŊŋŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽž «»'\(\)\-¨\,\;\.\:]+$/;

                // Filtrage des caractères
                function filterAddressInput(field) {
                    field.addEventListener('keypress', function(e) {
                        const char = e.key;
                        const currentValue = this.value;

                        // Si c'est le premier
                        if (currentValue.length === 0) {
                            if (!firstCharRegex.test(char)) {
                                e.preventDefault();
                                return false;
                            }
                        } else {
                            // Pour les caractères suivants
                            if (!
                                /^[a-zA-Z0-9àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĸĹĺĻļĽľĿŀŁłŃńŅņŇňŉŊŋŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽž «»'\(\)\-¨\,\;\.\:]$/
                                .test(char)) {
                                e.preventDefault();
                                return false;
                            }
                        }
                    });

                    // En cas de copier-coller
                    field.addEventListener('input', function(e) {
                        let value = this.value;

                        if (value.length > 0) {
                            // premier caractère
                            if (!firstCharRegex.test(value.charAt(0))) {
                                value = value.substring(1);
                            }

                            // Vérifier tous les caractères
                            if (!allowedCharsRegex.test(value)) {
                                // Suppr les caractères non autorisés
                                value = value.replace(
                                    /[^a-zA-Z0-9àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĸĹĺĻļĽľĿŀŁłŃńŅņŇňŉŊŋŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽž «»'\(\)\-¨\,\;\.\:]/g,
                                    '');
                            }

                            this.value = value;
                        }
                    });
                }

                // Appliquer ses restrictions aux champs Départ et Arrivée
                if (departureField) filterAddressInput(departureField);
                if (arrivalField) filterAddressInput(arrivalField);
            });
        </script>
    @endpush
</x-app-layout>
