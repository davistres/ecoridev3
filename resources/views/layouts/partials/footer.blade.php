<footer>
    <div class="h-[250px]">
        <img src="{{ asset('images/covoit_footer.jpg') }}" alt="Trois personnes dans une voiture"
            class="w-full h-full object-cover">
    </div>
    <div class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-6">
            <div class="flex flex-wrap justify-between items-start">
                <div class="w-full lg:w-1/3 mb-6 lg:mb-0">
                    <h3 class="text-lg font-bold mb-2">EcoRide</h3>
                    <p class="text-gray-400">Votre solution de covoiturage écologique.</p>
                </div>

                <div class="w-full lg:w-auto flex flex-wrap justify-between gap-x-16 sm:gap-x-8 md:gap-x-12 lg:gap-x-16">
                    <div class="w-full sm:w-auto mb-6 sm:mb-0">
                        <h4 class="font-semibold mb-2">Navigation</h4>
                        <ul>
                            <li><a href="{{ route('welcome') }}" class="text-gray-400 hover:text-white">Accueil</a></li>
                            <li><a href="{{ route('covoiturage') }}"
                                    class="text-gray-400 hover:text-white">Covoiturage</a>
                            </li>
                            <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white">Contact</a></li>
                        </ul>
                    </div>

                    <div class="w-full sm:w-auto mb-6 sm:mb-0">
                        <h4 class="font-semibold mb-2">Légal</h4>
                        <ul>
                            <li><a href="#" class="text-gray-400 hover:text-white">Mentions Légales</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Politique de
                                    confidentialité</a>
                            </li>
                        </ul>
                    </div>

                    <div class="w-full sm:w-auto">
                        <h4 class="font-semibold mb-2">Suivez-nous</h4>
                        <div class="flex space-x-4">
                            <a href="#"
                                class="text-gray-400 hover:text-white transform hover:scale-110 hover:shadow-lg transition duration-300 ease-in-out"><svg
                                    class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M22.46 6c-.77.35-1.6.58-2.46.67.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.27 0 .34.04.67.11.98-3.56-.18-6.72-1.88-8.84-4.48-.37.63-.58 1.37-.58 2.15 0 1.48.75 2.79 1.9 3.55-.7-.02-1.37-.22-1.95-.55v.05c0 2.07 1.47 3.8 3.42 4.19-.36.1-.74.15-1.13.15-.28 0-.55-.03-.81-.08.54 1.7 2.1 2.93 3.95 2.96-1.45 1.14-3.28 1.82-5.26 1.82-.34 0-.68-.02-1.01-.06 1.88 1.2 4.12 1.92 6.56 1.92 7.88 0 12.2-6.54 12.2-12.2 0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.22z">
                                    </path>
                                </svg></a>
                            <a href="#"
                                class="text-gray-400 hover:text-white transform hover:scale-110 hover:shadow-lg transition duration-300 ease-in-out"><svg
                                    class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.04C6.5 2.04 2 6.53 2 12.06c0 4.98 3.66 9.14 8.44 9.9v-7.02H7.97v-2.89h2.47V9.6c0-2.45 1.44-3.79 3.65-3.79.91 0 1.85.17 1.85.17v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.84h2.77l-.45 2.89h-2.32v7.02C18.34 21.2 22 17.04 22 12.06c0-5.53-4.5-10.02-10-10.02z">
                                    </path>
                                </svg></a>
                            <a href="#"
                                class="text-gray-400 hover:text-white transform hover:scale-110 hover:shadow-lg transition duration-300 ease-in-out"><svg
                                    class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 0C8.74 0 8.33 0.01 7.05 0.07c-1.28 0.06-2.16 0.25-2.93 0.55a4.87 4.87 0 0 0-1.77 1.15 4.87 4.87 0 0 0-1.15 1.77c-0.3 0.77-0.49 1.65-0.55 2.93C0.01 8.33 0 8.74 0 12s0.01 3.67 0.07 4.95c0.06 1.28 0.25 2.16 0.55 2.93a4.87 4.87 0 0 0 1.15 1.77 4.87 4.87 0 0 0 1.77 1.15c0.77 0.3 1.65 0.49 2.93 0.55C8.33 23.99 8.74 24 12 24s3.67-0.01 4.95-0.07c1.28-0.06 2.16-0.25 2.93-0.55a4.87 4.87 0 0 0 1.77-1.15 4.87 4.87 0 0 0 1.15-1.77c0.3-0.77 0.49-1.65 0.55-2.93C23.99 15.67 24 15.26 24 12s-0.01-3.67-0.07-4.95c-0.06-1.28-0.25-2.16-0.55-2.93a4.87 4.87 0 0 0-1.15-1.77A4.87 4.87 0 0 0 19.02 0.62c-0.77-0.3-1.65-0.49-2.93-0.55C15.67 0.01 15.26 0 12 0zm0 2.16c3.2 0 3.58 0.01 4.84 0.07 1.17 0.05 1.8 0.24 2.22 0.4 0.56 0.22 0.96 0.48 1.38 0.9 0.42 0.42 0.68 0.82 0.9 1.38 0.16 0.42 0.35 1.05 0.4 2.22 0.06 1.26 0.07 1.64 0.07 4.84s-0.01 3.58-0.07 4.84c-0.05 1.17-0.24 1.8-0.4 2.22-0.22 0.56-0.48 0.96-0.9 1.38-0.42 0.42-0.82 0.68-1.38 0.9-0.42 0.16-1.05 0.35-2.22 0.4-1.26 0.06-1.64 0.07-4.84 0.07s-3.58-0.01-4.84-0.07c-1.17-0.05-1.8-0.24-2.22-0.4-0.56-0.22-0.96-0.48-1.38-0.9-0.42-0.42-0.68-0.82-0.9-1.38-0.16-0.42-0.35-1.05-0.4-2.22C2.17 15.58 2.16 15.2 2.16 12s0.01-3.58 0.07-4.84c0.05-1.17 0.24-1.8 0.4-2.22 0.22 0.56 0.48 0.96 0.9-1.38 0.42 0.42 0.82 0.68 1.38-0.9 0.42-0.16 1.05 0.35 2.22 0.4C8.42 2.17 8.8 2.16 12 2.16zm0 5.43c-2.43 0-4.4 1.97-4.4 4.4s1.97 4.4 4.4 4.4 4.4-1.97 4.4-4.4-1.97-4.4-4.4-4.4zm0 7.16c-1.52 0-2.76-1.24-2.76-2.76s1.24-2.76 2.76-2.76 2.76 1.24 2.76 2.76-1.24 2.76-2.76 2.76zm6.36-7.72c-0.69 0-1.25 0.56-1.25 1.25s0.56 1.25 1.25 1.25 1.25-0.56 1.25-1.25-0.56-1.25-1.25-1.25z">
                                    </path>
                                </svg></a>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-gray-700">
            <div class="text-center text-gray-400">
                <p>&copy; {{ date('Y') }} EcoRide. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</footer>
