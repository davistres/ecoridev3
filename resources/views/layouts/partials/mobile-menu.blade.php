<div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform translate-x-full" @click.away="open = false"
    class="fixed right-0 bg-green-500 text-white md:hidden p-4 z-40 mt-16">
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
