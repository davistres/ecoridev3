<header class="bg-white shadow-md sticky top-0 z-50" x-data="{ open: false }">
    <nav class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- LOGO -->
            <a href="{{ route('welcome') }}" class="text-2xl font-bold text-green-600">
                EcoRide
            </a>

            <!-- Btn burger -->
            <div class="md:hidden">
                <button @click="open = !open"
                    class="text-gray-800 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition duration-300 ease-in-out">
                    <div class="w-6 h-6 relative">
                        <span :class="{ 'rotate-45': open, 'top-1/2 -translate-y-1/2': open }"
                            class="block absolute h-0.5 w-full bg-current transform transition-all duration-300 ease-in-out"></span>
                        <span :class="{ 'opacity-0': open }"
                            class="block absolute h-0.5 w-full bg-current transform transition-all duration-300 ease-in-out top-1/2 -translate-y-1/2"></span>
                        <span :class="{ '-rotate-45': open, 'top-1/2 -translate-y-1/2': open }"
                            class="block absolute h-0.5 w-full bg-current transform transition-all duration-300 ease-in-out bottom-0"></span>
                    </div>
                </button>
            </div>

            <!-- Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('welcome') }}"
                    class="nav-link relative {{ Request::is('/') || Request::is('accueil') ? 'font-bold after:scale-x-100' : '' }} text-gray-800 after:absolute after:left-0 after:bottom-0 after:h-0.5 after:w-full after:bg-green-500 after:transform after:scale-x-0 after:transition-transform after:duration-300 after:ease-in-out hover:after:scale-x-100 pb-1">Accueil</a>
                <a href="{{ route('covoiturage') }}"
                    class="nav-link relative {{ Request::is('covoiturage') ? 'font-bold after:scale-x-100' : '' }} text-gray-800 after:absolute after:left-0 after:bottom-0 after:h-0.5 after:w-full after:bg-green-500 after:transform after:scale-x-0 after:transition-transform after:duration-300 after:ease-in-out hover:after:scale-x-100 pb-1">Covoiturage</a>
                <a href="{{ route('contact') }}"
                    class="nav-link relative {{ Request::is('contact') ? 'font-bold after:scale-x-100' : '' }} text-gray-800 after:absolute after:left-0 after:bottom-0 after:h-0.5 after:w-full after:bg-green-500 after:transform after:scale-x-0 after:transition-transform after:duration-300 after:ease-in-out hover:after:scale-x-100 pb-1">Contact</a>

                @guest
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none">
                            Connexion
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-20" style="display: none;"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90">
                            <a href="{{ route('login') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white">Se
                                connecter</a>
                            <a href="{{ route('register') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white">S'enregistrer</a>
                        </div>
                    </div>
                @endguest

                @auth
                    <!-- TODO: lien vers dashboards -->
                    <!-- Ne pas oublié les employés -->
                    <a href="{{ route('dashboard') }}"
                        class="nav-link relative {{ Request::is('dashboard') ? 'font-bold after:scale-x-100' : '' }} text-gray-800 after:absolute after:left-0 after:bottom-0 after:h-0.5 after:w-full after:bg-green-500 after:transform after:scale-x-0 after:transition-transform after:duration-300 after:ease-in-out hover:after:scale-x-100 pb-1">
                        @if (auth()->user()->isAdmin())
                            ADMIN
                        @else
                            {{ auth()->user()->name }}
                        @endif
                    </a>
                    <!-- TODO: le panier si je l'utilise -->
                    <!-- Chercher un logo de panier pour que les utilisateurs retrouvent les trajets qu'ils ont sélectionnés. -->
                    <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                            class="nav-link relative text-gray-800 after:absolute after:left-0 after:bottom-0 after:h-0.5 after:w-full after:bg-green-500 after:transform after:scale-x-0 after:transition-transform after:duration-300 after:ease-in-out hover:after:scale-x-100 pb-1">
                            Déconnexion
                        </a>
                    </form>
                @endauth
            </div>
        </div>


    </nav>
    <nav aria-label="Menu mobile">
        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full" @click.away="open = false"
            class="fixed right-0 bg-green-500 text-white md:hidden p-4 z-40">
            <a href="{{ route('welcome') }}"
                class="block py-2 px-4 text-sm hover:bg-green-600 hover:font-bold hover:shadow-lg transition duration-300 ease-in-out">Accueil</a>
            <a href="{{ route('covoiturage') }}"
                class="block py-2 px-4 text-sm hover:bg-green-600 hover:font-bold hover:shadow-lg transition duration-300 ease-in-out">Covoiturage</a>
            <a href="{{ route('contact') }}"
                class="block py-2 px-4 text-sm hover:bg-green-600 hover:font-bold hover:shadow-lg transition duration-300 ease-in-out">Contact</a>
            <hr class="my-2 border-green-400" />
            @guest
                <a href="{{ route('login') }}"
                    class="block py-2 px-4 text-sm hover:bg-green-600 hover:font-bold hover:shadow-lg transition duration-300 ease-in-out">Se
                    connecter</a>
                <a href="{{ route('register') }}"
                    class="block py-2 px-4 text-sm hover:bg-green-600 hover:font-bold hover:shadow-lg transition duration-300 ease-in-out">S'enregistrer</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}"
                    class="block py-2 px-4 text-sm hover:bg-green-600 hover:font-bold hover:shadow-lg transition duration-300 ease-in-out">
                    @if (auth()->user()->isAdmin())
                        ADMIN
                    @else
                        {{ auth()->user()->name }}
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="block py-2 px-4 text-sm hover:bg-green-600 hover:font-bold hover:shadow-lg transition duration-300 ease-in-out">
                        Déconnexion
                    </a>
                </form>
            @endauth
        </div>
    </nav>
</header>
