document.addEventListener('DOMContentLoaded', function() {
    // La section filtres existe?
    const filtersSection = document.querySelector('.filters-section');
    if (!filtersSection) return;

    // Les élements des filtres:
    const ecoFilter = document.getElementById('eco-filter');
    const priceFilter = document.getElementById('price-filter');
    const priceValue = document.getElementById('price-value');
    const durationFilter = document.getElementById('duration-filter');
    const durationValue = document.getElementById('duration-value');
    const ratingFilter = document.getElementById('rating-filter');
    const stars = document.querySelectorAll('.rating-filter .star');
    const resetButton = document.getElementById('reset-filters-btn');

    // Éléments de la page
    const covoiturageCards = document.querySelectorAll('.covoiturage-card');
    const resultsCount = document.getElementById('results-count');

    // Init de l'affichage de la durée
    updateDurationDisplay();

    // Convert en mn
    function timeToMinutes(timeString) {
        if (!timeString) return 120;

        // Si c'est déjà un nombre, le retourner tel quel
        if (!isNaN(timeString)) {
            return Math.ceil(parseFloat(timeString));
        }

        // Format HH:MM:SS ou HH:MM
        const parts = timeString.split(':');
        if (parts.length >= 2) {
            const hours = parseInt(parts[0], 10) || 0;
            const minutes = parseInt(parts[1], 10) || 0;
            const seconds = parts.length > 2 ? parseInt(parts[2], 10) || 0 : 0;

            return Math.ceil(hours * 60 + minutes + seconds / 60);
        }

        return 120;
    }

    // Format heure et mn
    function formatDuration(minutes) {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;

        if (hours > 0) {
            return `${hours}h${mins > 0 ? ' ' + mins + 'min' : ''}`;
        } else {
            return `${mins}min`;
        }
    }

    // Maj la durée
    function updateDurationDisplay() {
        if (durationValue && durationFilter) {
            const minutes = parseInt(durationFilter.value);
            durationValue.textContent = formatDuration(minutes);
        }
    }

    // Maj le compteur de résultats
    function updateResultsCount() {
        const visibleCards = document.querySelectorAll('.covoiturage-card:not(.filtered-out)').length;
        if (resultsCount) {
            resultsCount.textContent = `${visibleCards} résultat(s) trouvé(s)`;
        }
    }

    // Appliquer tous les filtres
    function applyFilters() {
        const isEcoFilterActive = ecoFilter.checked;
        const maxPrice = parseInt(priceFilter.value);
        const maxDuration = parseInt(durationFilter.value);
        const minRating = parseInt(ratingFilter.value);

        let visibleCount = 0;

        covoiturageCards.forEach(card => {
            // => les données de la card
            const isEco = card.getAttribute('data-eco') === 'true';
            const price = parseInt(card.getAttribute('data-price')) || 0;
            const duration = timeToMinutes(card.getAttribute('data-max-travel-time') || '120');
            let rating = parseFloat(card.getAttribute('data-rating')) || 0;

            // Si la note est NaN ou 0, vérifier s'il y a un texte "Nouveau conducteur"
            if (!rating || isNaN(rating)) {
                const ratingElement = card.querySelector('.rating-value');
                if (ratingElement && ratingElement.textContent.includes('Nouveau conducteur')) {
                    rating = 0;
                }
            }

            // Appliquer les filtres
            const passesEcoFilter = !isEcoFilterActive || isEco;
            const passesPriceFilter = price <= maxPrice;

            // Pour la durée, si on est au max, on accepte tout
            const isMaxDuration = maxDuration === parseInt(durationFilter.max);
            const passesDurationFilter = isMaxDuration || duration <= (maxDuration + 5);

            const passesRatingFilter = rating >= minRating;

            // Afficher ou masquer la card
            if (passesEcoFilter && passesPriceFilter && passesDurationFilter && passesRatingFilter) {
                card.classList.remove('filtered-out');
                visibleCount++;
            } else {
                card.classList.add('filtered-out');
            }
        });

        // Maj le compteur
        updateResultsCount();

        // Message si aucun résultat
        showNoResultsMessage(visibleCount === 0);
    }

    // Afficher/masquer le message "aucun résultat"
    function showNoResultsMessage(show) {
        let noResultsMessage = document.querySelector('.no-results-filter-message');

        if (show && !noResultsMessage) {
            noResultsMessage = document.createElement('div');
            noResultsMessage.className = 'no-results-filter-message bg-yellow-50 border border-yellow-200 rounded-md p-4 mt-6 text-center';
            noResultsMessage.innerHTML = `
                <p class="text-yellow-800 mb-2">Aucun covoiturage ne correspond à vos critères de filtrage.</p>
                <button id="reset-filters-from-message" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                    Réinitialiser les filtres
                </button>
            `;

            const covoiturageList = document.querySelector('.covoiturage-list');
            if (covoiturageList) {
                covoiturageList.appendChild(noResultsMessage);

                // Ajouter l'event au nouveau bouton
                document.getElementById('reset-filters-from-message').addEventListener('click', resetFilters);
            }
        } else if (!show && noResultsMessage) {
            noResultsMessage.remove();
        }
    }

    // Réinit tous les filtres
    function resetFilters() {
        // Ecologique
        ecoFilter.checked = false;

        // Prix
        priceFilter.value = priceFilter.max;
        priceValue.textContent = priceFilter.max;

        // Durée
        durationFilter.value = durationFilter.max;
        updateDurationDisplay();

        // Note
        ratingFilter.value = 0;
        stars.forEach(star => star.classList.remove('active'));

        // Réappliquer les filtres
        applyFilters();
    }

    // addEventListener sur les filtres
    if (ecoFilter) {
        ecoFilter.addEventListener('change', applyFilters);
    }

    if (priceFilter && priceValue) {
        priceFilter.addEventListener('input', function() {
            priceValue.textContent = this.value;
            applyFilters();
        });
    }

    if (durationFilter && durationValue) {
        durationFilter.addEventListener('input', function() {
            updateDurationDisplay();
            applyFilters();
        });
    }

    // Gestion des étoiles pour la note
    if (stars.length > 0 && ratingFilter) {
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const clickedRating = parseInt(this.getAttribute('data-rating'));
                const currentRating = parseInt(ratingFilter.value);

                // Si on clic sur l'étoile déjà active, on la désactive
                if (clickedRating === currentRating) {
                    ratingFilter.value = 0;
                    stars.forEach(s => s.classList.remove('active'));
                } else {
                    ratingFilter.value = clickedRating;

                    // Maj => apparence des étoiles
                    stars.forEach(s => {
                        const starRating = parseInt(s.getAttribute('data-rating'));
                        if (starRating <= clickedRating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                }

                applyFilters();
            });
        });
    }

    // Btn réinit
    if (resetButton) {
        resetButton.addEventListener('click', resetFilters);
    }

    // Check si tous les covoits ont les mêmes valeurs
    checkSameValues();

    function checkSameValues() {
        if (covoiturageCards.length < 2) return;

        let allSamePrice = true;
        let firstPrice = null;
        let allSameDuration = true;
        let firstDuration = null;

        covoiturageCards.forEach(card => {
            const price = parseInt(card.getAttribute('data-price')) || 0;
            const duration = timeToMinutes(card.getAttribute('data-max-travel-time') || '120');

            if (firstPrice === null) {
                firstPrice = price;
            } else if (price !== firstPrice) {
                allSamePrice = false;
            }

            if (firstDuration === null) {
                firstDuration = duration;
            } else if (duration !== firstDuration) {
                allSameDuration = false;
            }
        });

        // Désactive le filtre prix si tous les prix sont identiques
        if (allSamePrice && priceFilter) {
            priceFilter.disabled = true;
            priceFilter.style.opacity = '0.5';
            const priceLabel = document.querySelector('label[for="price-filter"]');
            if (priceLabel) {
                priceLabel.innerHTML = 'Prix maximum: <span class="font-bold text-red-600">Tous les trajets ont le même prix</span>';
            }
        }

        // Désactive le filtre durée si toutes les durées sont identiques
        if (allSameDuration && durationFilter) {
            durationFilter.disabled = true;
            durationFilter.style.opacity = '0.5';
            const durationLabel = document.querySelector('label[for="duration-filter"]');
            if (durationLabel) {
                durationLabel.innerHTML = 'Durée maximale: <span class="font-bold text-red-600">Tous les trajets ont la même durée</span>';
            }
        }
    }
});
