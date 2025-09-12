<!-- Filtres pour les résultats de covoit -->
<section class="filters-section bg-white rounded-lg shadow-lg p-6 mb-8 max-w-4xl mx-auto">
    <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Filtrer les résultats</h2>

    <div class="filters-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Filtre écolo -->
        <div class="filter-group">
            <label for="eco-filter" class="flex items-center space-x-3 cursor-pointer">
                <input type="checkbox" id="eco-filter"
                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2">
                <span class="text-sm font-medium text-gray-700">Uniquement les voyages écologiques</span>
            </label>
        </div>

        <!-- Filtre prix -->
        <div class="filter-group">
            <label for="price-filter" class="block text-sm font-medium text-gray-700 mb-2">
                Prix maximum: <span id="price-value" class="font-bold text-green-600">{{ $max_price ?? 100 }}</span>
                crédits
            </label>
            <input type="range" id="price-filter"
                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                min="{{ $min_price ?? 0 }}" max="{{ $max_price ?? 100 }}" value="{{ $max_price ?? 100 }}"
                step="1">
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>{{ $min_price ?? 0 }}</span>
                <span>{{ $max_price ?? 100 }}</span>
            </div>
        </div>

        <!-- Filtre durée -->
        <div class="filter-group">
            <label for="duration-filter" class="block text-sm font-medium text-gray-700 mb-2">
                Durée maximale: <span id="duration-value" class="font-bold text-blue-600"></span>
            </label>
            <input type="range" id="duration-filter"
                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                min="{{ $min_duration ?? 30 }}" max="{{ $max_duration ?? 480 }}" value="{{ $max_duration ?? 480 }}"
                step="1">
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>{{ $min_duration_formatted ?? '30 min' }}</span>
                <span>{{ $max_duration_formatted ?? '8h' }}</span>
            </div>
        </div>

        <!-- Filtre note -->
        <div class="filter-group">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Note minimale du conducteur:
            </label>
            <div class="rating-filter flex space-x-1">
                <span class="star cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors"
                    data-rating="1">★</span>
                <span class="star cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors"
                    data-rating="2">★</span>
                <span class="star cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors"
                    data-rating="3">★</span>
                <span class="star cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors"
                    data-rating="4">★</span>
                <span class="star cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors"
                    data-rating="5">★</span>
                <input type="hidden" id="rating-filter" value="0">
            </div>
        </div>
    </div>

    <!-- Btn réinit -->
    <div class="mt-6 text-center">
        <button id="reset-filters-btn"
            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition-colors duration-300">
            Réinitialiser les filtres
        </button>
    </div>
</section>
