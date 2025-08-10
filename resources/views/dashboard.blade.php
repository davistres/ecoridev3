<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Espace') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Grand écran -->
            <div class="hidden md:grid md:grid-cols-3 md:grid-rows-2 gap-6">
                <div class="md:col-span-2 md:row-span-1">
                    @include('dashboard.partials.profil', ['user' => $user])
                </div>

                <div class="md:col-start-3 md:row-span-2 h-full flex flex-col">
                    <div class="flex-grow h-full">
                        @include('dashboard.partials.role')
                    </div>
                </div>

                <div class="md:col-span-2 md:row-start-2">
                    @include('dashboard.partials.reservations')
                </div>

                <div class="md:col-span-3 md:row-start-3">
                    @include('dashboard.partials.historique')
                </div>
            </div>

            <!-- Petit écran -->
            <div class="md:hidden space-y-6">
                @include('dashboard.partials.profil', ['user' => $user])
                @include('dashboard.partials.role')
                @include('dashboard.partials.reservations')
                @include('dashboard.partials.historique')
            </div>
        </div>
    </div>

    @include('dashboard.partials.popup')

</x-app-layout>
