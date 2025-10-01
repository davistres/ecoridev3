<!-- Composant "profil du conducteur" (photo, nom, note et nombre d'avis) que l'on retrouvera dans la modale "Détails du trajet", dans la page CONFIRMATION et dans la modale "Trajet planifié" -->
@props(['driver', 'avgRating', 'totalRatings'])

<div class="flex items-center space-x-4">
    @if ($driver->photo)
        <img src="data:{{ $driver->phototype }};base64,{{ base64_encode($driver->photo) }}"
            alt="Photo de {{ $driver->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-green-500">
    @else
        <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center border-2 border-green-500">
            <i class="fas fa-user text-gray-600 text-xl"></i>
        </div>
    @endif
    <div>
        <h5 class="font-semibold text-gray-800 text-lg">{{ $driver->name }}</h5>
        <div class="flex items-center">
            @if ($avgRating && $totalRatings > 0)
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= floor($avgRating))
                        <i class="fas fa-star text-yellow-400"></i>
                    @elseif($i - 0.5 <= $avgRating)
                        <i class="fas fa-star-half-alt text-yellow-400"></i>
                    @else
                        <i class="far fa-star text-gray-300"></i>
                    @endif
                @endfor
                <span class="ml-2 text-gray-600">({{ number_format($avgRating, 1) }}/5 sur {{ $totalRatings }}
                    avis)</span>
            @else
                <span class="text-gray-600">Nouveau conducteur</span>
            @endif
        </div>
    </div>
</div>
