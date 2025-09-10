<x-app-layout>

    <!-- Barre de recherche covoit -->
    <div class="bg-gray-100 py-20">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">Trouvez votre prochain covoiturage</h2>
            <p class="text-lg text-gray-600 mb-8">Économique, écologique et convivial.</p>

            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-4xl mx-auto">
                <!-- Info sur les codes postaux => cachée par défaut => apparait au focus sur les champs -->
                <div id="postal-code-info-welcome" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md"
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
                                <p>Les adresses doivent obligatoirement contenir un <strong>code postal</strong>
                                    (format: 12345 ou 12 345) pour effectuer la recherche.</p>
                                <p class="mt-1"><strong>Exemple :</strong> "123 rue de la Paix, 75001 Paris" ou "Gare
                                    SNCF 69000 Lyon"</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('covoiturage') }}" method="GET"
                    class="grid grid-cols-1 lg:grid-cols-10 gap-4 items-end">


                    <!-- Départ -->
                    <div class="lg:col-span-3">
                        <label for="departure" class="block text-sm font-medium text-gray-700 text-left">Départ</label>
                        <input type="text" id="departure" name="departure"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Ex: 123 rue de la Paix, 75001 Paris" required>
                    </div>

                    <!-- Arrivée -->
                    <div class="lg:col-span-3">
                        <label for="arrival" class="block text-sm font-medium text-gray-700 text-left">Arrivée</label>
                        <input type="text" id="arrival" name="arrival"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Ex: Gare SNCF 69000 Lyon" required>
                    </div>

                    <!-- Date -->
                    <div class="lg:col-span-2">
                        <label for="date" class="block text-sm font-medium text-gray-700 text-left">Date</label>
                        <input type="date" id="date" name="date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                    </div>

                    <!-- n place -->
                    <div class="lg:col-span-1">
                        <label for="seats" class="block text-sm font-medium text-gray-700 text-left">Places</label>
                        <input type="number" id="seats" name="seats" min="1" max="8"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="?" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
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
                <!-- Affichage des suggestions de dates alternatives-->
                @if (session('suggestions'))
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mt-6 text-left">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">💡 Suggestions de dates alternatives :</h4>
                        <div class="text-sm text-blue-700">
                            <p>Nous n'avons pas de covoiturage à la date recherchée. Néanmoins, nous en avons
                                @foreach (session('suggestions') as $index => $suggestion)
                                    @if ($index == 0)
                                        @if ($suggestion['count'] > 1)
                                            {{ $suggestion['count'] }} le <a href="#"
                                                class="suggestion-link font-medium underline hover:text-blue-900"
                                                data-date="{{ $suggestion['date'] }}"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}">{{ $suggestion['formatted_date'] }}</a>
                                            ({{ $suggestion['relative_day'] }})
                                        @else
                                            le <a href="#"
                                                class="suggestion-link font-medium underline hover:text-blue-900"
                                                data-date="{{ $suggestion['date'] }}"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}">{{ $suggestion['formatted_date'] }}</a>
                                            ({{ $suggestion['relative_day'] }})
                                        @endif
                                    @elseif($index == count(session('suggestions')) - 1)
                                        @if ($suggestion['count'] > 1)
                                            et {{ $suggestion['count'] }} le <a href="#"
                                                class="suggestion-link font-medium underline hover:text-blue-900"
                                                data-date="{{ $suggestion['date'] }}"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}">{{ $suggestion['formatted_date'] }}</a>
                                            ({{ $suggestion['relative_day'] }})
                                        @else
                                            et le <a href="#"
                                                class="suggestion-link font-medium underline hover:text-blue-900"
                                                data-date="{{ $suggestion['date'] }}"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}">{{ $suggestion['formatted_date'] }}</a>
                                            ({{ $suggestion['relative_day'] }})
                                        @endif
                                    @else
                                        @if ($suggestion['count'] > 1)
                                            , {{ $suggestion['count'] }} le <a href="#"
                                                class="suggestion-link font-medium underline hover:text-blue-900"
                                                data-date="{{ $suggestion['date'] }}"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}">{{ $suggestion['formatted_date'] }}</a>
                                            ({{ $suggestion['relative_day'] }})
                                        @else
                                            , le <a href="#"
                                                class="suggestion-link font-medium underline hover:text-blue-900"
                                                data-date="{{ $suggestion['date'] }}"
                                                data-depart="{{ session('lieu_depart') }}"
                                                data-arrivee="{{ session('lieu_arrivee') }}">{{ $suggestion['formatted_date'] }}</a>
                                            ({{ $suggestion['relative_day'] }})
                                        @endif
                                    @endif
                                @endforeach
                                ... Si vous êtes flexible, ils n'attendent que vous !
                            </p>
                        </div>
                    </div>

                    <!-- Formulaire caché pour les suggestions -->
                    <form id="suggestion-form-welcome" action="{{ route('covoiturage') }}" method="GET"
                        style="display: none;">
                        <input type="hidden" id="suggestion-departure-welcome" name="departure" value="">
                        <input type="hidden" id="suggestion-arrival-welcome" name="arrival" value="">
                        <input type="hidden" id="suggestion-date-welcome" name="date" value="">
                        <input type="hidden" id="suggestion-seats-welcome" name="seats" value="1">
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Présentation de l'entreprise -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Son histoire -->
            <div class="flex flex-wrap items-center mb-16">
                <div class="w-full md:w-1/2 px-6">
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">L'aventure EcoRide : bien plus qu'un simple
                        trajet
                    </h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        EcoRide est née d'une idée simple de notre fondateur, José. Passionné d'écologie et de
                        rencontres, il a imaginé une plateforme qui transformerait chaque voyage en une opportunité :
                        celle de préserver notre planète, de faire des économies et de tisser des liens forts.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Ce qui a commencé comme un petit projet familial est aujourd'hui une communauté grandissante de
                        voyageurs qui, comme vous, croient en un avenir plus durable et solidaire. Bienvenue dans notre
                        famille !
                    </p>
                </div>
                <div class="w-full md:w-1/2 px-6 mt-6 md:mt-0">
                    <img src="{{ asset('images/covoit_smile.jpg') }}" alt="Des gens souriants en covoiturage"
                        class="rounded-lg shadow-lg">
                </div>
            </div>

            <!-- 3 missions -->
            <div class="text-center my-16">
                <h3 class="text-3xl font-bold text-gray-800 mb-4">Nos Valeurs</h3>
                <p class="text-gray-600 max-w-2xl mx-auto">Au cœur de notre mission, trois piliers qui guident chacune
                    de nos actions.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-10 text-center">
                <div class="p-6">
                    <img src="{{ asset('images/ecolo.webp') }}" alt="Symbole de l'écologie"
                        class="mx-auto h-40 w-40 object-cover rounded-full shadow-md mb-5">
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">Un geste pour la planète</h4>
                    <p class="text-gray-600">
                        Chaque place libre occupée est une voiture en moins sur les routes. En covoiturant avec EcoRide,
                        vous participez activement à la réduction des émissions de CO2. Voyager n'a jamais été aussi
                        vert !
                    </p>
                </div>
                <div class="p-6">
                    <img src="{{ asset('images/econo.webp') }}" alt="Symbole des économies"
                        class="mx-auto h-40 w-40 object-cover rounded-full shadow-md mb-5">
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">Voyagez malin, dépensez moins</h4>
                    <p class="text-gray-600">
                        Partagez les frais de péage et de carburant et voyez la différence dans votre portefeuille. Le
                        covoiturage, c'est la solution intelligente pour voyager plus souvent, sans se ruiner.
                    </p>
                </div>
                <div class="p-6">
                    <img src="{{ asset('images/commu.webp') }}" alt="Symbole de la communauté"
                        class="mx-auto h-40 w-40 object-cover rounded-full shadow-md mb-5">
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">Créez des liens, pas seulement des trajets
                    </h4>
                    <p class="text-gray-600">
                        EcoRide, c'est plus qu'une simple mise en relation. C'est une communauté de voyageurs qui
                        partagent des histoires, des rires et des expériences. Votre prochain meilleur ami est peut-être
                        à un covoiturage de distance.
                    </p>
                </div>
            </div>

            <!-- Facile à utiliser -->
            <div class="flex flex-wrap items-center mt-20">
                <div class="w-full md:w-1/2 px-6">
                    <img src="{{ asset('images/appli.jpg') }}" alt="Application EcoRide sur un téléphone"
                        class="rounded-lg shadow-lg">
                </div>
                <div class="w-full md:w-1/2 px-6 mt-6 md:mt-0">
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">La simplicité au bout des doigts</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Nous avons conçu une expérience utilisateur intuitive et sans tracas. Grâce à notre barre de
                        recherche efficace, trouvez le trajet qui vous correspond en quelques clics.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Indiquez votre départ, votre arrivée, la date de votre voyage et le nombre de places souhaitées,
                        et laissez-nous faire le reste. Réserver un covoiturage n'a jamais été aussi simple !
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Choix de la date => à partir de la date d'aujourd'hui
            const dateInput = document.getElementById('date');
            const today = new Date();
            const year = today.getFullYear();
            const month = ('0' + (today.getMonth() + 1)).slice(-2);
            const day = ('0' + today.getDate()).slice(-2);
            dateInput.min = `${year}-${month}-${day}`;

            // Avertissement utilisateur si ils choissisent moins de 5 places
            const seatsInput = document.getElementById('seats');
            const seatsWarning = document.getElementById('seats-warning');
            seatsInput.addEventListener('input', function() {
                const seats = parseInt(this.value, 10);
                if (seats >= 5) {
                    seatsWarning.style.display = 'block';
                } else {
                    seatsWarning.style.display = 'none';
                }
            });

            // Affichage des info sur les codes postaux
            const departureInput = document.getElementById('departure');
            const arrivalInput = document.getElementById('arrival');
            const postalCodeInfo = document.getElementById('postal-code-info-welcome');

            // Afficher les infos
            function showPostalCodeInfo() {
                postalCodeInfo.style.display = 'block';
            }

            // Even pour afficher l'info au focus (champs départ/arrivée)
            if (departureInput) {
                departureInput.addEventListener('focus', showPostalCodeInfo);
            }
            if (arrivalInput) {
                arrivalInput.addEventListener('focus', showPostalCodeInfo);
            }

            // Liens de suggestions => gestion du clic sur un lien pour relancer auto une nouvelle recherche
            const suggestionLinks = document.querySelectorAll('.suggestion-link');
            suggestionLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Récupére les données
                    const date = this.getAttribute('data-date');
                    const depart = this.getAttribute('data-depart');
                    const arrivee = this.getAttribute('data-arrivee');

                    // Rempli le formulaire caché
                    document.getElementById('suggestion-departure-welcome').value = depart;
                    document.getElementById('suggestion-arrival-welcome').value = arrivee;
                    document.getElementById('suggestion-date-welcome').value = date;

                    // Et on soumet le formulaire
                    document.getElementById('suggestion-form-welcome').submit();
                });
            });
        });
    </script>

    <script>
        // Restriction des caractères pour les champs Départ et Arrivée
        document.addEventListener('DOMContentLoaded', function() {
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

                    // Si c'est le premoier
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

</x-app-layout>
