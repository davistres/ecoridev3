<header class="bg-white shadow-md sticky top-0 z-50" x-data="{ open: false }">
    <nav class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            // LOGO
            <a href="{{ route('welcome') }}" class="text-2xl font-bold text-green-600">
                EcoRide
            </a>

            // Btn burger
            <div class="md:hidden">
                <button @click="open = !open"
                    class="text-gray-800 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition duration-300 ease-in-out">
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform rotate-90"
                        x-transition:enter-end="opacity-100 transform rotate-0">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        style="display: none;" x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform rotate-0"
                        x-transition:leave-end="opacity-0 transform rotate-90">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            // Menu
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('welcome') }}"
                    class="nav-link {{ Request::is('/') || Request::is('accueil') ? 'font-bold border-b-2 border-green-500' : '' }} text-gray-800 hover:font-bold hover:border-b-2 hover:border-green-500 pb-1 transition duration-300 ease-in-out transform hover:scale-105">Accueil</a>
                <a href="{{ route('covoiturage') }}"
                    class="nav-link {{ Request::is('covoiturage') ? 'font-bold border-b-2 border-green-500' : '' }} text-gray-800 hover:font-bold hover:border-b-2 hover:border-green-500 pb-1 transition duration-300 ease-in-out transform hover:scale-105">Covoiturage</a>
                <a href="{{ route('contact') }}"
                    class="nav-link {{ Request::is('contact') ? 'font-bold border-b-2 border-green-500' : '' }} text-gray-800 hover:font-bold hover:border-b-2 hover:border-green-500 pb-1 transition duration-300 ease-in-out transform hover:scale-105">Contact</a>

                @guest
                    <a href="{{ route('login') }}"
                        class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300 ease-in-out">Connexion</a>
                @endguest

                @auth
                    // TODO: lien vers dashboards
                    <a href="#" class="text-gray-800 hover:text-blue-600 pb-1">
                        @if (auth()->user()->isAdmin())
                            ADMIN
                        @else
                            {{ auth()->user()->name }}
                        @endif
                    </a>
                    // TODO: le panier si je l'utilise
                    /*
                    Chercher un logo de panier pour que les utilisateurs retrouvent les trajets qu'ils ont sélectionnés.

                    */
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-gray-800 hover:text-blue-600 pb-1">
                            Déconnexion
                        </a>
                    </form>
                @endauth
            </div>
        </div>

        // Menu mobile
        <div x-show="open" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform -translate-x-full" @click.away="open = false"
            class="fixed inset-0 bg-green-500 text-white w-3/4 md:w-1/2 lg:w-1/3 p-4 z-50">
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
                <a href="#"
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
